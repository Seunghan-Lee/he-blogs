(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    var searchInput = document.getElementById("wp-block-search__input-1");
    if (!searchInput) {
      return;
    }

    searchInput.setAttribute("placeholder", "검색어를 입력해주세요");
  });
})();
