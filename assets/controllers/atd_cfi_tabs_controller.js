import {Controller} from 'stimulus';

export default class extends Controller {
    static targets = ['anchors', 'contents'];
    static values = {};

    connect() {
        this.switchContent(window.location.hash ? window.location.hash : null);
        Array.from(this.anchorsTarget.children).forEach(c => c.addEventListener('click', this.changeAnchor));
    }

    changeAnchor = (e) => {
        e.preventDefault();
        e.stopPropagation();
        this.switchContent(e.target.hash);
    }

    switchContent(id) {
        const anchor = this.findContentById(id);

        this.activateAnchor(anchor.id);

        Array.from(this.contentsTarget.children).forEach(c => c.classList.remove('open'));
        anchor.classList.add('open');
    }

    activateAnchor(id) {
        Array.from(this.anchorsTarget.children).forEach(c => c.classList.remove('active'));
        const anchor = this.findAnchorById(id);
        anchor.classList.add('active');
    }

    findAnchorById(id) {
        const anchor = Array.from(this.anchorsTarget.children).filter(c => {
            return c.hash.slice(1) === id
        });

        if (!anchor) {
            return this.anchorsTarget.children[0];
        }

        return anchor[0];
    }

    findContentById(id) {
        if (!id) {
            id = this.anchorsTarget.children[0].hash;
        }

        const anchor = this.contentsTarget.children.namedItem(id.slice(1));

        if (!anchor) {
            this.findContentById(null);
        }

        return anchor;
    }
}