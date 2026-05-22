(function () {
  function getUtils() {
    return window.Utils || {
      escapeHtml(value) {
        return String(value ?? '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;');
      },
    };
  }

  class EmploymentTrendsChart {
    static render(target, data) {
      const container = typeof target === 'string' ? document.querySelector(target) : target;
      if (!container) return;

      const utils = getUtils();
      const entries = Array.isArray(data)
        ? data
        : Object.entries(data || {}).map(([label, value]) => ({ label, value }));

      if (!entries.length) {
        container.innerHTML = '<div class="text-center text-secondary">No data</div>';
        return;
      }

      const normalized = entries.map((item) => ({
        label: item.label || item.name || item.status || 'Unknown',
        value: Number(item.value ?? item.count ?? 0),
      }));
      const max = Math.max(...normalized.map((item) => item.value), 1);
      const total = normalized.reduce((sum, item) => sum + item.value, 0) || 1;

      container.innerHTML = `
        <div class="chart-shell chart-shell-bars">
          <div class="chart-head">
            <div>
              <div class="chart-subtitle">Workforce</div>
              <div class="chart-title">Employment Trends</div>
            </div>
            <div class="chart-pill">${normalized.length} categories</div>
          </div>
          <div class="chart-list">
            ${normalized
              .map((item, index) => {
                const percent = Math.round((item.value / total) * 100);
                const width = Math.max((item.value / max) * 100, item.value > 0 ? 8 : 0);
                return `
                  <div class="chart-row">
                    <div class="chart-row-label">
                      <span class="chart-index">${index + 1}</span>
                      <span class="truncate">${utils.escapeHtml(item.label)}</span>
                    </div>
                    <div class="chart-row-track"><div class="chart-row-fill" style="width:${width}%;"></div></div>
                    <div class="chart-row-meta">${item.value} · ${percent}%</div>
                  </div>
                `;
              })
              .join('')}
          </div>
        </div>
      `;
    }
  }

  window.EmploymentTrendsChart = EmploymentTrendsChart;
})();
