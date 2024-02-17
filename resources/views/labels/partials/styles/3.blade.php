<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        color-adjust: exact;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    @media print {
        .priceTags {
            border: 1px dashed rgba(0, 0, 0, 0.5) !important;
        }

        .priceTags .header {
            border-bottom: 1px solid #647d83 !important;
            background-color: #a4d2ad !important;
        }

        .priceTags .header .logo {
            background-color: #fff !important;
        }

        .priceTags .footer {
            background-color: #a4d2ad !important;
        }
    }

    .priceTags {
        color: #20383e;
        position: relative;
        width: 5cm;
        height: 3cm;
        border: 1px dashed rgba(0, 0, 0, 0.5);
    }

    .priceTags .header {
        position: relative;
        width: 100%;
        padding: 4px;
        padding-left: 6px;
        padding-right: 6px;
        border-bottom: 1px solid #647d83;
        background-color: #a4d2ad;
        display: flex;
        justify-content: space-between;
    }

    .priceTags .header .text {
        font-size: 10px;
        font-weight: 400;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .priceTags .header .text .title {
        /* font-size: 12px; */
        z-index: 2;
        white-space: nowrap;
        text-transform: uppercase;
        font-weight: 600;
    }

    .priceTags .header .logo {
        position: absolute;
        right: 6px;
        top: 4px;
        bottom: 4px;
        z-index: 1;
        width: 30px;
        height: 30px;
        border-radius: 9999px;
        background-color: #fff;
    }

    .priceTags .body {
        padding: 2px;
        padding-left: 12px;
        padding-right: 12px;
    }

    .priceTags .body table {
        font-size: 10px;
        padding: 0;
        margin: 0;
    }

    .priceTags .body table tr td:nth-child(2),
    .priceTags .body table tr td:nth-child(3) {
        font-size: 12px;
        font-weight: 600;
    }

    .priceTags .footer {
        position: absolute;
        bottom: 0;
        left: 0;
        background-color: #a4d2ad;
        width: 100%;
        padding: 1px;
        padding-left: 4px;
        padding-right: 4px;
        font-size: 6px;
        font-style: italic;
        text-transform: uppercase;
    }
</style>