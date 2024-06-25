const express = require("express");
const cors = require("cors");

const app = express();

const port = process.env.PORT || 9876;

app.use(cors());

const appStoreLink = "https://apps.apple.com/us/app/kizuner/id1524617131";
const playStoreLink = "https://play.google.com/store/apps/details?id=com.kizuner";
const apiLink = "https://kizuner-st.inapps.technology/api/share/";

const buildShareContent = function (data) {
    const imageUrl = data.image_url.replace("https", "http");

    return `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <meta property="fb:app_id" content="1239520603104986" />
            <meta property="og:title" content="${data.title}" />
            <meta property="og:description" content="${data.description}" />
            <meta property="og:type" content="website" />
            <meta property="og:url" content="https://kizuner.com" />
            <meta property="og:image" content="${imageUrl}" />
            <meta property="og:image:url" content="${imageUrl}" />
            <meta property="og:image:secure_url" content="${data.image_url}" />
            <meta property="og:image:width" content="1200" />
            <meta property="og:image:height" content="630" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" content="${data.title}" />
            <meta name="twitter:description" content="${data.description}" />
            <meta name="twitter:image" content="${data.image_url}" />
            <title>${data.title}</title>
        </head>
        <body>
        <script>
            window.location.href = "${data.storeLink}";
        </script>
        </body>
        </html>
    `;
};

app.get("/k", (req, res) => {
    const userAgent = req.headers['user-agent'];
    const { t, d, i, k, id } = req.query;

    const title = decodeURIComponent(t) || "Do What You Love - Kizuner";
    const description = decodeURIComponent(d);
    const image_id = decodeURIComponent(i);
    const image_url =
        image_id && image_id !== "undefined"
            ? `https://storage.googleapis.com/kizuner-storage-live/${image_id}`
            : "https://kizuner.com/wp-content/uploads/2020/07/Untitled-1.jpg";
    const dynamicLink = `${apiLink}${k}/${id}`;

    let storeLink = playStoreLink
    if (/android/i.test(userAgent)) {
        // res.redirect('https://play.google.com/store/apps/details?id=com.yourapp');
    } else if (/iphone|ipad|ipod/i.test(userAgent)) {
        storeLink = appStoreLink
        storeLink = `kizunerapp://${k}/${id}`
        // res.redirect('https://apps.apple.com/us/app/yourapp/idYOUR_APP_ID');
    }

    return res.send(
        buildShareContent({
            kind: k,
            id: id,
            title,
            description,
            image_url,
            storeLink,
            dynamicLink
        })
    );
});

app.listen(port, () => {
    console.log(`Started at ${port}`);
});
