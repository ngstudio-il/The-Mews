// ---- Settings: fill these in before going live ----
const CONFIG = {
  // WhatsApp number in international format, digits only (e.g. "972501234567")
  whatsappNumber: "",
  whatsappText: {
    he: "היי, אשמח לקבל פרטים על פרויקט The Mews בלימסול",
    en: "Hi, I'd like details about The Mews project in Limassol",
  },
  // URL that receives the lead form as a POST (e.g. a CRM, Zapier, Make or Formspree webhook)
  formEndpoint: "",
};

// Language (Hebrew in the markup, English from i18n.js)
let lang = "he";
const t = (key) => (lang === "en" ? I18N_EN : I18N_HE)[key] ?? key;
const metaDescription = document.querySelector('meta[name="description"]');
const heText = { "meta.title": document.title, "meta.description": metaDescription.content };

function setLang(next) {
  lang = next;
  const dict = lang === "en" ? I18N_EN : heText;
  document.querySelectorAll("[data-i18n]").forEach((el) => {
    const key = el.dataset.i18n;
    if (!(key in heText)) heText[key] = el.innerHTML;
    if (key in dict) el.innerHTML = dict[key];
  });
  document.querySelectorAll("[data-i18n-ph]").forEach((el) => {
    const key = el.dataset.i18nPh;
    if (!(key in heText)) heText[key] = el.placeholder;
    if (key in dict) el.placeholder = dict[key];
  });
  document.title = dict["meta.title"];
  metaDescription.content = dict["meta.description"];
  document.documentElement.lang = lang;
  document.documentElement.dir = lang === "en" ? "ltr" : "rtl";
  document.querySelectorAll("[data-lang-toggle]").forEach((b) => {
    b.textContent = lang === "en" ? "עברית" : "EN";
    b.setAttribute("aria-label", lang === "en" ? "החלפה לעברית" : "Switch to English");
  });
  document.querySelectorAll(".form-msg").forEach((m) => { m.textContent = ""; m.className = "form-msg"; });
  updateWhatsApp();
  document.dispatchEvent(new Event("langchange"));
  try { localStorage.setItem("lang", lang); } catch {}
}

document.querySelectorAll("[data-lang-toggle]").forEach((b) =>
  b.addEventListener("click", () => setLang(lang === "en" ? "he" : "en"))
);

// Header appears once the hero is scrolled past
const header = document.querySelector(".site-header");
const hero = document.querySelector(".hero");
const onScroll = () => header.classList.toggle("visible", window.scrollY > hero.offsetHeight - 120);
onScroll();
window.addEventListener("scroll", onScroll, { passive: true });

// Hero video: lighter portrait cut on phones, poster only for reduced motion
const heroVideo = document.querySelector(".hero-video");
if (heroVideo && !matchMedia("(prefers-reduced-motion: reduce)").matches) {
  const base = matchMedia("(max-width: 640px)").matches ? heroVideo.dataset.mobile : heroVideo.dataset.desktop;
  heroVideo.innerHTML = `<source src="${base}.mp4" type="video/mp4"><source src="${base}.webm" type="video/webm">`;
  heroVideo.load();
  heroVideo.play().catch(() => {});
}

// WhatsApp link
const wa = document.getElementById("whatsapp");
function updateWhatsApp() {
  if (CONFIG.whatsappNumber) {
    wa.href = `https://wa.me/${CONFIG.whatsappNumber}?text=${encodeURIComponent(CONFIG.whatsappText[lang])}`;
  } else {
    wa.href = "#contact";
    wa.removeAttribute("target");
  }
}

let savedLang = null;
try { savedLang = localStorage.getItem("lang"); } catch {}
setLang(location.hash === "#en" || savedLang === "en" ? "en" : "he");

// Lead forms
document.querySelectorAll(".lead-form").forEach((form) => {
  const msg = form.querySelector(".form-msg");
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const name = form.elements.name;
    const phone = form.elements.phone;

    const okName = name.value.trim().length >= 2;
    const okPhone = /^[+\d][\d\s-]{7,}$/.test(phone.value.trim());
    name.classList.toggle("invalid", !okName);
    phone.classList.toggle("invalid", !okPhone);

    msg.className = "form-msg";
    if (!okName || !okPhone) {
      msg.textContent = t("form.invalid");
      msg.classList.add("err");
      return;
    }

    const button = form.querySelector("button");
    button.disabled = true;
    try {
      if (CONFIG.formEndpoint) {
        const res = await fetch(CONFIG.formEndpoint, {
          method: "POST",
          headers: { "Content-Type": "application/json", Accept: "application/json" },
          body: JSON.stringify({
            name: name.value.trim(),
            phone: phone.value.trim(),
            project: "The Mews Residences",
            language: lang,
            page: location.href,
          }),
        });
        if (!res.ok) throw new Error(res.status);
      }
      form.reset();
      msg.textContent = t("form.ok");
      msg.classList.add("ok");
    } catch {
      msg.textContent = t("form.error");
      msg.classList.add("err");
    } finally {
      button.disabled = false;
    }
  });
});

// Gallery lightbox
const lb = document.getElementById("lightbox");
const lbImg = lb.querySelector("img");
document.querySelectorAll(".g-item").forEach((item) => {
  item.addEventListener("click", () => {
    lbImg.src = item.dataset.src;
    lbImg.alt = item.querySelector("img").alt;
    lb.hidden = false;
  });
});
lb.addEventListener("click", (e) => {
  if (e.target !== lbImg) lb.hidden = true;
});
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") lb.hidden = true;
});

// Page width without the scrollbar, for aligning full-bleed layouts to .container
const setPageWidth = () => document.documentElement.style.setProperty("--page-w", `${document.documentElement.clientWidth}px`);
setPageWidth();
window.addEventListener("resize", setPageWidth);

// Project section: image wipes in, text panel rises over it (only if it starts below the fold)
const project = document.querySelector(".project");
if (project && "IntersectionObserver" in window && !matchMedia("(prefers-reduced-motion: reduce)").matches
    && project.getBoundingClientRect().top > window.innerHeight) {
  project.classList.add("pending");
  const pio = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
      project.classList.remove("pending");
      pio.disconnect();
    }
  }, { threshold: 0.25 });
  pio.observe(project);
}

// Reveal on scroll
const revealTargets = document.querySelectorAll(".section-head, .split-text, .split-media, .card, .g-item, .faq details, .cta-text");
if ("IntersectionObserver" in window) {
  const io = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("in");
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  revealTargets.forEach((el) => {
    el.classList.add("reveal");
    io.observe(el);
  });
}

document.getElementById("year").textContent = new Date().getFullYear();
