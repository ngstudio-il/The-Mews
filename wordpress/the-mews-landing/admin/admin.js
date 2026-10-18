/* The Mews admin: media pickers and repeaters */
(function ($) {
  // Media picker (image or video) from the WordPress media library
  $(document).on("click", ".tml-pick", function (e) {
    e.preventDefault();
    const box = $(this).closest(".tml-media");
    const type = box.data("type");
    const frame = wp.media({
      title: type === "video" ? "בחירת סרטון" : "בחירת תמונה",
      library: { type: type },
      multiple: false,
    });
    frame.on("select", function () {
      const file = frame.state().get("selection").first().toJSON();
      box.find("input").val(file.url);
      box.find(".tml-preview").html(
        type === "video"
          ? $("<video muted preload='metadata'>").attr("src", file.url)
          : $("<img alt=''>").attr("src", file.url)
      );
    });
    frame.open();
  });

  $(document).on("click", ".tml-clear", function (e) {
    e.preventDefault();
    const box = $(this).closest(".tml-media");
    box.find("input").val("");
    box.find(".tml-preview").empty();
  });

  // Repeaters: add, remove and drag to reorder rows. Row indexes are renumbered before submit.
  $(document).on("click", ".tml-add", function () {
    const rep = $(this).closest(".tml-repeater");
    const html = rep.children("template.tml-row-template").html();
    rep.children(".tml-rows").append(html.replace(/\[__i__\]/g, "[" + Date.now() + "]"));
  });

  $(document).on("click", ".tml-remove", function () {
    if (window.confirm("להסיר את הפריט?")) $(this).closest(".tml-row").remove();
  });

  $(document).on("click", ".tml-reset", function (e) {
    if (!window.confirm("להחזיר את כל השדות בלשונית הזו לתוכן המקורי?")) e.preventDefault();
  });

  $(function () {
    $(".tml-rows").sortable({ handle: ".tml-handle", axis: "y", tolerance: "pointer" });
  });
})(jQuery);
