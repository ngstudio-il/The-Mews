// Floating accessibility menu. Builds its own markup so every page can include just this script.
(function () {
  const TEXT = {
    he: {
      open: "תפריט נגישות",
      title: "נגישות",
      close: "סגירה",
      bigger: "הגדלת טקסט",
      smaller: "הקטנת טקסט",
      contrast: "ניגודיות גבוהה",
      gray: "גווני אפור",
      links: "הדגשת קישורים",
      font: "גופן קריא",
      motion: "עצירת אנימציות",
      reset: "איפוס הגדרות",
      statement: "הצהרת נגישות",
    },
    en: {
      open: "Accessibility menu",
      title: "Accessibility",
      close: "Close",
      bigger: "Larger text",
      smaller: "Smaller text",
      contrast: "High contrast",
      gray: "Grayscale",
      links: "Highlight links",
      font: "Readable font",
      motion: "Stop animations",
      reset: "Reset settings",
      statement: "Accessibility statement",
    },
  };
  const TOGGLES = ["contrast", "gray", "links", "font", "motion"];
  const MAX_TEXT = 3;
  const root = document.documentElement;

  let state = { text: 0, contrast: false, gray: false, links: false, font: false, motion: false };
  try { Object.assign(state, JSON.parse(localStorage.getItem("a11y") || "{}")); } catch {}

  const widget = document.createElement("div");
  widget.className = "a11y-widget";
  widget.innerHTML = `
    <button type="button" class="a11y-toggle" aria-expanded="false" aria-controls="a11y-panel">
      <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="4" r="2"/><path d="M4 8.5c2.6.8 5.3 1.2 8 1.2s5.4-.4 8-1.2M12 9.7V15m0 0-3.2 6.2M12 15l3.2 6.2"/></svg>
    </button>
    <div class="a11y-panel" id="a11y-panel" role="dialog" hidden>
      <div class="a11y-head">
        <b data-a11y="title"></b>
        <button type="button" class="a11y-close" data-a11y-label="close">×</button>
      </div>
      <div class="a11y-grid">
        <button type="button" data-action="bigger"><span class="a11y-ico">A+</span><span data-a11y="bigger"></span></button>
        <button type="button" data-action="smaller"><span class="a11y-ico">A−</span><span data-a11y="smaller"></span></button>
        <button type="button" data-action="contrast" aria-pressed="false"><svg class="a11y-svg" viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7.5"/><path d="M10 2.5a7.5 7.5 0 0 1 0 15z" class="fill"/></svg><span data-a11y="contrast"></span></button>
        <button type="button" data-action="gray" aria-pressed="false"><svg class="a11y-svg" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 2.5c3 3.6 5 6.4 5 9a5 5 0 0 1-10 0c0-2.6 2-5.4 5-9z"/><path d="M10 6v10.5a5 5 0 0 0 5-5c0-1.7-1.6-4-5-5.5z" class="fill"/></svg><span data-a11y="gray"></span></button>
        <button type="button" data-action="links" aria-pressed="false"><span class="a11y-ico"><u>ab</u></span><span data-a11y="links"></span></button>
        <button type="button" data-action="font" aria-pressed="false"><span class="a11y-ico">Aa</span><span data-a11y="font"></span></button>
        <button type="button" data-action="motion" aria-pressed="false"><span class="a11y-ico">❚❚</span><span data-a11y="motion"></span></button>
        <button type="button" data-action="reset"><span class="a11y-ico">↺</span><span data-a11y="reset"></span></button>
      </div>
      <a class="a11y-statement" href="accessibility.html" data-a11y="statement"></a>
    </div>`;
  document.body.appendChild(widget);

  const toggle = widget.querySelector(".a11y-toggle");
  const panel = widget.querySelector(".a11y-panel");

  function labels() {
    const t = TEXT[root.lang === "en" ? "en" : "he"];
    widget.querySelectorAll("[data-a11y]").forEach((el) => { el.textContent = t[el.dataset.a11y]; });
    widget.querySelectorAll("[data-a11y-label]").forEach((el) => el.setAttribute("aria-label", t[el.dataset.a11yLabel]));
    toggle.setAttribute("aria-label", t.open);
    panel.setAttribute("aria-label", t.title);
  }

  function apply() {
    for (let i = 1; i <= MAX_TEXT; i++) root.classList.toggle(`a11y-text-${i}`, state.text === i);
    TOGGLES.forEach((k) => {
      root.classList.toggle(`a11y-${k}`, state[k]);
      widget.querySelector(`[data-action="${k}"]`).setAttribute("aria-pressed", String(state[k]));
    });
    document.querySelectorAll("video").forEach((v) => (state.motion ? v.pause() : v.autoplay && v.play().catch(() => {})));
    try { localStorage.setItem("a11y", JSON.stringify(state)); } catch {}
  }

  function setOpen(open) {
    panel.hidden = !open;
    toggle.setAttribute("aria-expanded", String(open));
    if (open) panel.querySelector("[data-action]").focus();
  }

  toggle.addEventListener("click", () => setOpen(panel.hidden));
  widget.querySelector(".a11y-close").addEventListener("click", () => { setOpen(false); toggle.focus(); });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !panel.hidden) { setOpen(false); toggle.focus(); }
  });
  document.addEventListener("click", (e) => {
    if (!panel.hidden && !widget.contains(e.target)) setOpen(false);
  });

  widget.querySelectorAll("[data-action]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const a = btn.dataset.action;
      if (a === "bigger") state.text = Math.min(MAX_TEXT, state.text + 1);
      else if (a === "smaller") state.text = Math.max(0, state.text - 1);
      else if (a === "reset") state = { text: 0, contrast: false, gray: false, links: false, font: false, motion: false };
      else state[a] = !state[a];
      apply();
    });
  });

  document.addEventListener("langchange", labels);
  labels();
  apply();
})();
