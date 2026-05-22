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

  class UserGrowthChart {
    static render(target, data) {
      const container = typeof target === 'string' ? document.querySelector(target) : target;
      if (!container) return;

      const utils = getUtils();
      const entries = Array.isArray(data) ? data : [];

      if (!entries.length) {
        container.innerHTML = '<div class="text-center text-secondary">No data</div>';
        return;
      }

      const values = entries.map((item) => Number(item.count || 0));
      const max = Math.max(...values, 1);
      const width = 560;
      const height = 220;
      const stepX = entries.length > 1 ? width / (entries.length - 1) : width;
      const points = entries
        .map((item, index) => {
          const value = Number(item.count || 0);
          const x = entries.length > 1 ? index * stepX : width / 2;
          const y = height - (value / max) * (height - 24) - 12;
          return `${x},${y}`;
        })
        .join(' ');
      const areaPath = `M 0 ${height} L ${entries
        .map((item, index) => {
          const value = Number(item.count || 0);
          const x = entries.length > 1 ? index * stepX : width / 2;
          const y = height - (value / max) * (height - 24) - 12;
          return `${x} ${y}`;
        })
        .join(' L ')} L ${width} ${height} Z`;

      container.innerHTML = `
        <div class="chart-shell chart-shell-line">
          <div class="chart-head">
            <div>
              <div class="chart-subtitle">Trend</div>
              <div class="chart-title">User Growth</div>
            </div>
            <div class="chart-pill">${entries.length} months</div>
          </div>
          <div class="line-chart-wrap">
            <svg viewBox="0 0 ${width} ${height}" class="line-chart" preserveAspectRatio="none" aria-label="User growth chart">
              <defs>
                <linearGradient id="userGrowthAreaGradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="rgba(37, 99, 235, 0.28)" />
                  <stop offset="100%" stop-color="rgba(37, 99, 235, 0.02)" />
                </linearGradient>
              </defs>
              <path d="${areaPath}" fill="url(#userGrowthAreaGradient)"></path>
              <polyline points="${points}" fill="none" stroke="var(--primary-600)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></polyline>
              ${entries
                .map((item, index) => {
                  const value = Number(item.count || 0);
                  const x = entries.length > 1 ? index * stepX : width / 2;
                  const y = height - (value / max) * (height - 24) - 12;
                  return `
                    <g>
                      <circle cx="${x}" cy="${y}" r="5" fill="var(--color-surface)" stroke="var(--primary-600)" stroke-width="3"></circle>
                      <text x="${x}" y="${height - 2}" text-anchor="middle" class="line-chart-label">${utils.escapeHtml(item.month || item.month_label || '')}</text>
                      <text x="${x}" y="${Math.max(y - 12, 14)}" text-anchor="middle" class="line-chart-value">${value}</text>
                    </g>
                  `;
                })
                .join('')}
            </svg>
          </div>
        </div>
      `;
    }
  }

  window.UserGrowthChart = UserGrowthChart;
})();
