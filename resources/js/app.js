import Alpine from 'alpinejs';
import { registerToast } from './toast';
import { registerUi } from './ui';
import { initPageProgress, initReveal } from './reveal';

window.Alpine = Alpine;

registerToast(Alpine);
registerUi(Alpine);

Alpine.start();

initReveal();
initPageProgress();
