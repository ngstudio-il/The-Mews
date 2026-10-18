// ---- Settings: fill these in before going live ----
const CONFIG = {
  // WhatsApp number in international format, digits only (e.g. "972501234567")
  whatsappNumber: "",
  whatsappText: "היי, אשמח לקבל פרטים על פרויקט The Mews בלימסול",
  // URL that receives the lead form as a POST (e.g. a CRM, Zapier, Make or Formspree webhook)
  formEndpoint: "",
};

// Header style on scroll
const header = document.querySelector(".site-header");
const onScroll = () => header.classList.toggle("scrolled", window.scrollY > 40);
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
if (CONFIG.whatsappNumber) {
  wa.href = `https://wa.me/${CONFIG.whatsappNumber}?text=${encodeURIComponent(CONFIG.whatsappText)}`;
} else {
  wa.href = "#contact";
  wa.removeAttribute("target");
}

// Lead forms
document.querySelectorAll(".lead-form").forEach((form) => {
  const msg = form.querySelector(".form-msg");
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const name = form.elements.name;
    const phone = form.elements.phone;
    const email = form.elements.email;
    const consent = form.elements.consent;

    const okName = name.value.trim().length >= 2;
    const okPhone = /^[+\d][\d\s-]{7,}$/.test(phone.value.trim());
    const okEmail = !email.value.trim() || /^\S+@\S+\.\S+$/.test(email.value.trim());
    name.classList.toggle("invalid", !okName);
    phone.classList.toggle("invalid", !okPhone);
    email.classList.toggle("invalid", !okEmail);
    consent.closest(".consent").classList.toggle("invalid", !consent.checked);

    msg.className = "form-msg";
    if (!okName || !okPhone || !okEmail || !consent.checked) {
      msg.textContent = "נא למלא שם וטלפון תקינים ולאשר את התנאים";
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
            email: email.value.trim(),
            project: "The Mews Residences",
            page: location.href,
          }),
        });
        if (!res.ok) throw new Error(res.status);
      }
      form.reset();
      msg.textContent = "תודה! הפרטים התקבלו ונחזור אליכם בהקדם.";
      msg.classList.add("ok");
    } catch {
      msg.textContent = "אירעה שגיאה בשליחה, נסו שוב או פנו אלינו בוואטסאפ.";
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

// Reveal on scroll
const revealTargets = document.querySelectorAll(".section-head, .split-text, .split-media, .card, .g-item, .steps li, .faq details, .cta-text");
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
