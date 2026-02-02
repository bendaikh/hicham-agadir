import './bootstrap';
import './search';
import { reportsManager } from './reports';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Register the reportsManager factory with Alpine
Alpine.data('reportsManager', reportsManager);

Alpine.start();
