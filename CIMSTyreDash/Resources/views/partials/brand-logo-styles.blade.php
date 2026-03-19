{{-- Brand logo styles - included via @push('styles') from sidebar --}}
<style>
    .brand-logo-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 30px;
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 4px;
        padding: 3px 5px;
        flex-shrink: 0;
        overflow: hidden;
    }
    .brand-logo-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: block;
    }
    .brand-logo-box-md {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 40px;
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 4px;
        padding: 4px 6px;
        flex-shrink: 0;
        overflow: hidden;
    }
    .brand-logo-box-md img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: block;
    }
    .brand-logo-box-lg {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100px;
        height: 50px;
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 5px;
        padding: 5px 8px;
        flex-shrink: 0;
        overflow: hidden;
    }
    .brand-logo-box-lg img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: block;
    }
    /* Product tyre image thumbnails */
    .product-img-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 2px;
        flex-shrink: 0;
        overflow: hidden;
        cursor: pointer;
    }
    .product-img-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: block;
    }
    .product-img-box-lg {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 3px;
        flex-shrink: 0;
        overflow: hidden;
        cursor: pointer;
    }
    .product-img-box-lg img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: block;
    }
    /* Product image modal/popup */
    .product-img-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .product-img-modal-overlay img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
</style>
