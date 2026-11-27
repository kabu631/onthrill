import { HttpErrorResponse, HttpInterceptorFn, HttpResourceRef, HttpResponse } from '@angular/common/http';
import { inject } from '@angular/core';
import { of } from 'rxjs';
import { Site } from './site';

/**
 * Every request to Laravel asks for JSON (so validation errors come back as 422
 * with the messages per field, never as a redirect).
 */
export const jsonInterceptor: HttpInterceptorFn = (req, next) =>
  next(req.clone({ setHeaders: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }));

/** Answers the first page's request with the data that came with the page. */
export const firstPageInterceptor: HttpInterceptorFn = (req, next) => {
  if (req.method === 'GET') {
    const data = inject(Site).takeFirstPage(req.urlWithParams);
    if (data !== undefined) {
      return of(new HttpResponse({ status: 200, body: data, url: req.urlWithParams }));
    }
  }
  return next(req);
};

/** /api/packages?destination=x&sort=y: empty values left out, names in order (as Laravel builds them). */
export function apiUrl(path: string, params: Record<string, string | number | null | undefined> = {}): string {
  const query = Object.keys(params)
    .filter((key) => params[key] !== null && params[key] !== undefined && params[key] !== '')
    .sort()
    .map((key) => encodeURIComponent(key) + '=' + encodeURIComponent(String(params[key])))
    .join('&');
  return path + (query ? '?' + query : '');
}

/** HTTP status of a failed resource (404 for a page that does not exist), or 0. */
export function errorStatus(resource: HttpResourceRef<unknown>): number {
  const error = resource.error();
  return error instanceof HttpErrorResponse ? error.status : error ? 500 : 0;
}

/** Laravel's validation messages, one per field. */
export function fieldErrors(error: unknown): Record<string, string> {
  if (error instanceof HttpErrorResponse && error.status === 422 && error.error?.errors) {
    const out: Record<string, string> = {};
    for (const [field, messages] of Object.entries(error.error.errors as Record<string, string[]>)) {
      out[field] = messages[0];
    }
    return out;
  }
  return {};
}

/** A message for the person when a form could not be sent at all. */
export function errorMessage(error: unknown, fallback: string): string {
  if (error instanceof HttpErrorResponse) {
    if (error.status === 429) {
      return 'Too many attempts. Please wait a minute and try again.';
    }
    if (error.status === 419) {
      return 'This page was open for a long time. Please reload it and try again.';
    }
    if (error.error?.message && error.status !== 422 && error.status < 500) {
      return error.error.message;
    }
  }
  return fallback;
}
