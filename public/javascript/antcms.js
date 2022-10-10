const _ = (el) => document.querySelector(el);
const storage = window.localStorage;

// theme switcher
const themeSwitcher = {
  _scheme: "auto",

  change: {
    light: "<i>Cambiar a modo oscuro</i>",
    dark: "<i>Cambiar a modo claro</i>",
  },

  buttonsTarget: ".theme-switcher",

  localStorageKey: "picoPreferedColorScheme",

  /**
   * Get scheme from storage
   */
  get schemeFromLocalStorage() {
    return void 0 !== storage && null !== storage.getItem(this.localStorageKey)
      ? storage.getItem(this.localStorageKey)
      : this._scheme;
  },

  /**
   * Get prefered color scheme
   */
  get preferedColorScheme() {
    return window.matchMedia("(prefers-color-scheme: dark)").matches
      ? "dark"
      : "light";
  },

  /**
   * Set scheme
   */
  set scheme(param) {
    "auto" === param
      ? this.switchScheme(this.preferedColorScheme)
      : ("dark" !== param && "light" !== param) || (this._scheme = param),
      this.applyScheme(),
      this.schemeToLocalStorage();
  },

  /**
   * Get scheme
   */
  get scheme() {
    return this._scheme;
  },

  /**
   * Switch scheme
   *
   * @param string param
   * @returns string
   */
  switchScheme(param) {
    return "dark" === param ? (this.scheme = "light") : (this.scheme = "dark");
  },

  /**
   * Init switcher
   */
  initSwitcher() {
    _(this.buttonsTarget).addEventListener("click", () =>
      this.switchScheme(this.scheme)
    );
  },

  createBtn(event) {
    let element = document.createElement(event.tag);
    element.className = event.class;
    element.type = event.type;
    document.querySelector(event.target).appendChild(element);
  },

  applyScheme() {
    document.querySelector("html").setAttribute("data-theme", this.scheme);
    const theme = "dark" === this.scheme ? this.change.dark : this.change.light;
    _(this.buttonsTarget).innerHTML = theme;
    _(this.buttonsTarget).setAttribute(
      "aria-label",
      theme.replace(/<[^>]*>?/gm, "")
    );
  },

  schemeToLocalStorage() {
    void 0 !== storage && storage.setItem(this.localStorageKey, this.scheme);
  },

  init() {
    this.scheme = this.schemeFromLocalStorage;
    this.initSwitcher();
  },
};
// create button
themeSwitcher.createBtn({
  tag: "BUTTON",
  type: "button",
  class: "contrast switcher theme-switcher",
  target: "body",
});
// init theme switcher events
themeSwitcher.init();

// toggle menu
let bool = false;
_(".btn-menu").addEventListener("click", (evt) => {
  bool = !bool;
  _(".btn-menu").className = bool ? "btn-menu active" : "btn-menu";
  _(".menu").className = bool ? "menu active" : "menu";
});
