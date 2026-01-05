/**
 * Frontend Main Entry Point
 * This file bundles all frontend modules for production
 * 
 * Usage:
 * - Development: Use individual module files (current setup)
 * - Production: Use bundled version from Vite (npm run build)
 */

// Core modules
import '../../public/assets/frontend/js/modules/utils.js';
import '../../public/assets/frontend/js/modules/wishlist.js';
import '../../public/assets/frontend/js/modules/cart.js';

// Script modules
import '../../public/assets/frontend/js/modules/script-utils.js';
import '../../public/assets/frontend/js/modules/carousels.js';
import '../../public/assets/frontend/js/modules/search.js';

// Main scripts
import '../../public/assets/frontend/js/script.js';
import '../../public/assets/frontend/js/functions.js';

