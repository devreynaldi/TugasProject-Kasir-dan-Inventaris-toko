/* TokoKu — efek antarmuka: hitung naik, cahaya mengikuti kursor, tilt, ripple, jaringan latar */
(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fmt = n => new Intl.NumberFormat('id-ID').format(Math.round(n));

  // Angka naik (data-count, data-prefix)
  function countUp(el) {
    const target = Number(el.dataset.count) || 0, pre = el.dataset.prefix || '';
    if (reduce || target === 0) { el.textContent = pre + fmt(target); return; }
    const t0 = performance.now(), dur = 1400;
    const tick = t => {
      const p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 4);
      el.textContent = pre + fmt(target * e);
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }

  // Cahaya kursor pada panel + tilt 3D pada .tilt (delegasi, jadi elemen Alpine ikut bekerja)
  document.addEventListener('pointermove', e => {
    const g = e.target.closest && e.target.closest('.bg-white, .glass, .tilt');
    if (g) {
      const r = g.getBoundingClientRect();
      g.style.setProperty('--mx', (e.clientX - r.left) + 'px');
      g.style.setProperty('--my', (e.clientY - r.top) + 'px');
    }
    const t = e.target.closest && e.target.closest('.tilt');
    if (t && !reduce) {
      const r = t.getBoundingClientRect();
      const px = (e.clientX - r.left) / r.width - .5, py = (e.clientY - r.top) / r.height - .5;
      t.style.transform = `perspective(700px) rotateX(${(-py * 9).toFixed(2)}deg) rotateY(${(px * 9).toFixed(2)}deg)`;
    }
  }, { passive: true });
  document.addEventListener('pointerout', e => {
    const t = e.target.closest && e.target.closest('.tilt');
    if (t && !t.contains(e.relatedTarget)) t.style.transform = '';
  });

  // Ripple pada tombol utama
  document.addEventListener('click', e => {
    const b = e.target.closest && e.target.closest('.bg-indigo-600, .bg-gray-800');
    if (!b || reduce) return;
    const r = b.getBoundingClientRect(), s = document.createElement('span');
    s.className = 'ripple';
    s.style.cssText = `width:40px;height:40px;left:${e.clientX - r.left - 20}px;top:${e.clientY - r.top - 20}px`;
    b.appendChild(s);
    setTimeout(() => s.remove(), 650);
  });

  // Jaringan titik di latar
  function initNet() {
    const c = document.getElementById('bg-net'); if (!c) return;
    const ctx = c.getContext('2d'), mouse = { x: -999, y: -999 };
    let w, h, nodes = [];
    const size = () => {
      const dpr = Math.min(devicePixelRatio || 1, 2);
      w = innerWidth; h = innerHeight;
      c.width = w * dpr; c.height = h * dpr; c.style.width = w + 'px'; c.style.height = h + 'px';
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      const n = Math.min(70, Math.floor(w * h / 26000));
      nodes = Array.from({ length: n }, () => ({ x: Math.random() * w, y: Math.random() * h, vx: (Math.random() - .5) * .25, vy: (Math.random() - .5) * .25 }));
    };
    const line = (x1, y1, x2, y2, col) => { ctx.strokeStyle = col; ctx.beginPath(); ctx.moveTo(x1, y1); ctx.lineTo(x2, y2); ctx.stroke(); };
    const draw = () => {
      ctx.clearRect(0, 0, w, h);
      for (let i = 0; i < nodes.length; i++) {
        const a = nodes[i];
        if (!reduce) { a.x += a.vx; a.y += a.vy; if (a.x < 0 || a.x > w) a.vx *= -1; if (a.y < 0 || a.y > h) a.vy *= -1; }
        ctx.fillStyle = 'rgba(41,228,255,.7)'; ctx.beginPath(); ctx.arc(a.x, a.y, 1.4, 0, 6.283); ctx.fill();
        for (let j = i + 1; j < nodes.length; j++) {
          const b = nodes[j], dx = a.x - b.x, dy = a.y - b.y, d = dx * dx + dy * dy;
          if (d < 18000) line(a.x, a.y, b.x, b.y, `rgba(139,92,246,${(.3 * (1 - d / 18000)).toFixed(3)})`);
        }
        const mx = a.x - mouse.x, my = a.y - mouse.y, md = mx * mx + my * my;
        if (md < 26000) line(a.x, a.y, mouse.x, mouse.y, `rgba(41,228,255,${(.55 * (1 - md / 26000)).toFixed(3)})`);
      }
      if (!reduce) requestAnimationFrame(draw);
    };
    addEventListener('resize', () => { size(); if (reduce) draw(); });
    addEventListener('pointermove', e => { mouse.x = e.clientX; mouse.y = e.clientY; }, { passive: true });
    size(); draw();
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-count]').forEach(countUp);
    document.querySelectorAll('[data-stagger] > *').forEach((el, i) => el.style.setProperty('--i', i));
    initNet();
  });
})();
