// quill-image-resize-module.js
class ImageResize {
    constructor(quill, options = {}) {
      this.quill = quill;
      this.options = options;
      this.handle = null;
      this.box = null;
      this.img = null;
  
      this.quill.root.addEventListener('click', (evt) => {
        if (evt.target && evt.target.tagName && evt.target.tagName.toUpperCase() === 'IMG') {
          this.selectImage(evt.target);
        } else if (this.img) {
          this.deselectImage();
        }
      });
  
      document.addEventListener('click', (evt) => {
        if (this.img && !this.quill.root.contains(evt.target)) {
          this.deselectImage();
        }
      });
    }
  
    selectImage(img) {
      if (this.img === img) return;
      this.deselectImage();
      this.img = img;
      this.show();
    }
  
    deselectImage() {
      if (this.handle) {
        this.handle.parentNode.removeChild(this.handle);
        this.handle = null;
      }
      if (this.box) {
        this.box.parentNode.removeChild(this.box);
        this.box = null;
      }
      this.img = null;
    }
  
    show() {
      if (!this.img) return;
  
      this.box = document.createElement('div');
      Object.assign(this.box.style, {
        position: 'absolute',
        border: '1px dashed gray',
        zIndex: 100,
        cursor: 'pointer',
      });
  
      const rect = this.img.getBoundingClientRect();
      this.box.style.left = `${rect.left + window.pageXOffset}px`;
      this.box.style.top = `${rect.top + window.pageYOffset}px`;
      this.box.style.width = `${rect.width}px`;
      this.box.style.height = `${rect.height}px`;
  
      document.body.appendChild(this.box);
  
      this.handle = document.createElement('div');
      Object.assign(this.handle.style, {
        width: '12px',
        height: '12px',
        backgroundColor: 'white',
        border: '1px solid gray',
        position: 'absolute',
        bottom: '-6px',
        right: '-6px',
        cursor: 'nwse-resize',
      });
      this.box.appendChild(this.handle);
  
      this.handle.addEventListener('mousedown', this.startResize.bind(this));
    }
  
    startResize(event) {
      event.preventDefault();
      event.stopPropagation();
  
      this.startX = event.clientX;
      this.startY = event.clientY;
      this.startWidth = this.img.width;
      this.startHeight = this.img.height;
  
      document.addEventListener('mousemove', this.resizeMove);
      document.addEventListener('mouseup', this.stopResize);
    }
  
    resizeMove = (event) => {
      if (!this.img) return;
      const dx = event.clientX - this.startX;
      const dy = event.clientY - this.startY;
      this.img.width = this.startWidth + dx;
      this.img.height = this.startHeight + dy;
  
      this.show(); // Refresh box
    };
  
    stopResize = () => {
      document.removeEventListener('mousemove', this.resizeMove);
      document.removeEventListener('mouseup', this.stopResize);
    };
  }
  
  // Exportar compatible con Angular / ES
  export default ImageResize;
  