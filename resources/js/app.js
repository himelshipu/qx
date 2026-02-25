import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import ApexCharts from 'apexcharts';
import { createPopper } from '@popperjs/core';

window.Alpine = Alpine;
window.flatpickr = flatpickr;
window.ApexCharts = ApexCharts;
window.createPopper = createPopper;

Alpine.store('theme', {
    init() {
        const savedTheme = localStorage.getItem('theme');
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        this.theme = savedTheme || systemTheme;
        this.applyTheme();
    },
    
    theme: 'light',
    
    toggle() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', this.theme);
        this.applyTheme();
    },
    
    applyTheme() {
        const html = document.documentElement;
        const body = document.body;
        
        if (this.theme === 'dark') {
            html.classList.add('dark');
            body.classList.add('dark', 'bg-gray-900', 'text-gray-100');
        } else {
            html.classList.remove('dark');
            body.classList.remove('dark', 'bg-gray-900', 'text-gray-100');
            body.classList.add('bg-white', 'text-gray-900');
        }
    }
});

// Sidebar Store - Admin only (Click only, no hover)
Alpine.store('sidebar', {
    init() {
        this.isExpanded = window.innerWidth >= 1280;
    },
    
    isExpanded: true,
    isMobileOpen: false,
    
    toggleExpanded() {
        this.isExpanded = !this.isExpanded;
        this.isMobileOpen = false;
    },
    
    toggleMobileOpen() {
        this.isMobileOpen = !this.isMobileOpen;
    },
    
    setMobileOpen(val) {
        this.isMobileOpen = val;
    }
});

// Start Alpine - IMPORTANT: Do this after defining stores
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        Alpine.start();
        initializeCharts();
        initializeCalendar();
    });
} else {
    Alpine.start();
    initializeCharts();
    initializeCalendar();
}

// Initialize charts for admin pages
function initializeCharts() {
    // Chart 1
    if (document.querySelector('#chartOne')) {
        import('./components/admin/chart/chart-1').then(module => module.initChartOne()).catch(() => {});
    }
    // Chart 2
    if (document.querySelector('#chartTwo')) {
        import('./components/admin/chart/chart-2').then(module => module.initChartTwo()).catch(() => {});
    }
    // Chart 3
    if (document.querySelector('#chartThree')) {
        import('./components/admin/chart/chart-3').then(module => module.initChartThree()).catch(() => {});
    }
    // Chart 6
    if (document.querySelector('#chartSix')) {
        import('./components/admin/chart/chart-6').then(module => module.initChartSix()).catch(() => {});
    }
    // Chart 8
    if (document.querySelector('#chartEight')) {
        import('./components/admin/chart/chart-8').then(module => module.initChartEight()).catch(() => {});
    }
    // Chart 13
    if (document.querySelector('#chartThirteen')) {
        import('./components/admin/chart/chart-13').then(module => module.initChartThirteen()).catch(() => {});
    }
}

// Initialize calendar for admin pages
function initializeCalendar() {
    if (document.querySelector('#calendar')) {
        import('./components/admin/calendar-init').then(module => module.calendarInit()).catch(() => {});
    }
}
