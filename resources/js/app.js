import './bootstrap';
import Chart from 'chart.js/auto';

window.Chart = Chart;

const SIDEBAR_KEY = 'sidebar-collapsed';

function initSidebar() {
    const layout = document.getElementById('app-layout');
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');

    if (!layout || !toggle || !sidebar) {
        return;
    }

    const applyState = (collapsed) => {
        layout.classList.toggle('sidebar-collapsed', collapsed);
        toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        toggle.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
    };

    applyState(localStorage.getItem(SIDEBAR_KEY) === 'true');

    toggle.addEventListener('click', () => {
        const collapsed = !layout.classList.contains('sidebar-collapsed');
        applyState(collapsed);
        localStorage.setItem(SIDEBAR_KEY, collapsed ? 'true' : 'false');
    });
}

document.addEventListener('DOMContentLoaded', initSidebar);
