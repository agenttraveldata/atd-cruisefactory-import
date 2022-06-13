import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static values = {endpoint: String, param: Object};
    static targets = ['results'];

    connect() {
        if (this.endpointValue) {
            this.getResults();
        }
    }

    getResults() {
        const formData = new FormData();
        formData.append('__atd_cfi_idString', true);

        if (this.paramValue) {
            for (const k in this.paramValue) {
                formData.append(k, this.paramValue[k]);
            }
        }

        fetch(`${this.endpointValue}?${new URLSearchParams(formData)}`).then(r => r.json()).then(json => {
            this.resultsTarget.innerHTML = '';

            if (json.length > 0) {
                json.forEach(row => {
                    if (row.hasOwnProperty('html_response')) {
                        this.resultsTarget.insertAdjacentHTML('beforeend', row.html_response);
                    }
                });
            } else {
                this.resultsTarget.insertAdjacentHTML('beforeend', '<div>No results have been found. Please try searching to find something!</div>');
            }
        });
    }
}
