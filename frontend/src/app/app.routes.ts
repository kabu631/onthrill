import { Routes } from '@angular/router';

/** The same addresses as the Laravel routes in routes/web.php (each page is loaded when first opened). */
export const routes: Routes = [
  { path: '', loadComponent: () => import('./pages/home/home').then((m) => m.HomePage) },

  { path: 'destinations', loadComponent: () => import('./pages/destinations').then((m) => m.DestinationsPage) },
  { path: 'destinations/country/:country', loadComponent: () => import('./pages/destinations').then((m) => m.DestinationsPage) },
  { path: 'destinations/:slug', loadComponent: () => import('./pages/destination').then((m) => m.DestinationPage) },

  { path: 'packages', loadComponent: () => import('./pages/packages').then((m) => m.PackagesPage) },
  { path: 'packages/type/:category', loadComponent: () => import('./pages/packages').then((m) => m.PackagesPage) },
  { path: 'packages/:slug', loadComponent: () => import('./pages/package/package').then((m) => m.PackagePage) },
  { path: 'booking/:reference', loadComponent: () => import('./pages/booking').then((m) => m.BookingPage) },

  { path: 'contact-us', loadComponent: () => import('./pages/contact').then((m) => m.ContactPage) },
  { path: 'gallery', loadComponent: () => import('./pages/gallery').then((m) => m.GalleryPage) },
  { path: 'search', loadComponent: () => import('./pages/search').then((m) => m.SearchPage) },

  { path: 'login', loadComponent: () => import('./pages/account/auth').then((m) => m.LoginPage) },
  { path: 'register', loadComponent: () => import('./pages/account/auth').then((m) => m.RegisterPage) },
  { path: 'forgot-password', loadComponent: () => import('./pages/account/auth').then((m) => m.ForgotPasswordPage) },
  { path: 'reset-password/:token', loadComponent: () => import('./pages/account/auth').then((m) => m.ResetPasswordPage) },
  { path: 'my-account', loadComponent: () => import('./pages/account/account').then((m) => m.AccountPage) },
  { path: 'my-account/bookings/:reference', loadComponent: () => import('./pages/booking').then((m) => m.BookingPage), data: { inAccount: true } },

  { path: 'not-found', loadComponent: () => import('./pages/not-found').then((m) => m.NotFoundPage) },
  { path: ':slug', loadComponent: () => import('./pages/cms-page').then((m) => m.CmsPage) },
  { path: '**', loadComponent: () => import('./pages/not-found').then((m) => m.NotFoundPage) },
];
