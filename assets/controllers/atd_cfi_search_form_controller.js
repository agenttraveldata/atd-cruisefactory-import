import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static values = {};
    static targets = ['dropDown'];
    apiPath = '/wp-json/atd/cfi/v1/search/options';
    defaultValues = {};
    formData;

    connect() {
        this.dropDownTargets.forEach(e => {
            if (!this.defaultValues.hasOwnProperty(e.name)) {
                this.defaultValues[e.name] = [];
            }

            if (e.value !== '') {
                this.defaultValues[e.name].push(e.value);
            }
        });

        this.formData = new FormData(this.element);
        const hasUrlParams = window.location.search !== ''
            ? window.location.search
            : window.location.hash !== ''
                ? window.location.hash
                : null;
        if (hasUrlParams) {
            const urlSearchParams = new URLSearchParams(hasUrlParams.replace(/^#/, '?'));
            urlSearchParams.forEach((v, k) => {
                if (v && this.formData.has(k)) {
                    this.formData.set(k, v);
                }
            });
            this.update(null, this.formData);
        } else {
            this.update(this.formData);
        }
    }

    populateDropDowns = (d) => {
        Object.entries(d).forEach(([key, value]) => {
            if (key.match(/month$/)) {
                this.sortAndPopulateDropDown(`${key}_from`, value);
                this.sortAndPopulateDropDown(`${key}_to`, value);
                return;
            }

            this.sortAndPopulateDropDown(key, value);
        });
    }

    sortAndPopulateDropDown = (key, value) => {
        const el = this.element.querySelector(`#${key}`);
        const dateKeys = atd_cfi.date_keys;

        if (el) {
            const firstTitle = el.options[0].text;
            el.innerHTML = '';
            el.options.add(new Option(firstTitle, ''));

            Object.entries(value).sort(dateKeys.includes(key) ? this.compareKeyDates : this.compareKeyStrings).forEach(([id, name]) => {
                el.options.add(new Option(name, id, false,
                    this.formData.has(key) && this.formData.get(key) === id
                ));
            });
        }
    }

    compareKeyStrings = ([, a], [, b]) => {
        return a.localeCompare(b);
    }

    compareKeyDates = ([, a], [, b]) => Date.parse(new Date(a)) - Date.parse(new Date(b));

    update(e, formData = null) {
        if (!formData) {
            this.formData = new FormData(this.element);
        }

        this.doFetch(`${this.apiPath}?${new URLSearchParams(this.formData)}`);
    }

    reset() {
        this.formData = new FormData();
        this.element.querySelectorAll('input[type="hidden"]').forEach(el => {
            if (el.name === "") {
                return;
            }

            this.formData.set(el.name, el.value);
        });

        const el = this.element.querySelector('input[id="atd_cf_keyword"]');
        if (el) {
            el.setAttribute('value', '');
            el.value = '';
        }

        this.doFetch(`${this.apiPath}?${new URLSearchParams(this.formData)}`);
    }

    doFetch = (fetchPath) => {
        this.element.insertAdjacentHTML('beforeend', `<div id="atd-cf-search-form-spinner"><div class="spinner-loader"></div></div>`)
        fetch(`${fetchPath}`).then(r => r.json()).then(this.populateDropDowns)
            .finally(() => {
                document.getElementById('atd-cf-search-form-spinner').remove();
            });
    }

    snakeToWords = (e) => {
        return this.capitalizeFirstLetter(e.replace(/_([a-z])/g, (g) => ` ${g[1].toUpperCase()}`));
    }

    capitalizeFirstLetter = (s) => {
        return s[0].toUpperCase() + s.slice(1);
    }
}
