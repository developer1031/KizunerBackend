const express = require("express");
const cors = require("cors");

const app = express();

const port = process.env.PORT || 9876;

app.use(cors());

const BASEURL = "https://kizuner-st.inapps.technology/api/share/"
const LinkAndroid = "https://play.google.com/store/apps/details?id=com.kizuner"
const LinkIPhone = "https://apps.apple.com/us/app/kizuner/id1524617131"

app.get("/", (req, res) => {
    return res.send(
        buildShareContent({
            dynamicLink: "https://kizuner.com",
            title: "Do What You Love - Kizuner",
            description: "",
            image_url:
                "https://kizuner.com/wp-content/uploads/2020/07/Untitled-1.jpg"
        })
    );
});

const buildShareContent = function (data) {
    return `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta property="fb:app_id" content="1239520603104986" />
            <meta property="al:android:package" content="com.kizuner" />
            <meta property="al:android:app_name" content="Kizuner" />
            <meta property="al:android:url" content="${LinkAndroid}" />
            <meta property="al:ios:url" content="${LinkIPhone}" />
            <meta property="al:ios:app_store_id" content="1524617131" />
            <meta property="al:ios:app_name" content="Kizuner" />
            <meta property="al:web:should_fallback" content="true" />
            <meta property="al:web:url" content="https://kizuner.com" />
            <meta property="og:title" content="${data.title}" />
            <meta property="og:description" content="${data.description}" />
            <meta property="og:image" content="${data.image_url}" />
            <meta property="og:image:width" content="1200" />
            <meta property="og:image:height" content="630" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:creator" content="@kizuner" />
            <meta name="twitter:title" content="${data.title}" />
            <meta name="twitter:description" content="${data.description}" />
            <meta name="twitter:image" content="${data.image_url}" />
            <title>${data.title}</title>
        </head>
        <body>
            <script>
                function getMobileOperatingSystem() {
                    const userAgent = navigator.userAgent || navigator.vendor || window.opera;
                    if (/windows phone/i.test(userAgent)) {
                        return "Windows Phone";
                    }
                    if (/android/i.test(userAgent)) {
                        return "Android";
                    }
                    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                        return "iOS";
                    }
                    return "unknown";
                }
                
                window.onload = function() {
                    const os = getMobileOperatingSystem();

                    if (os == "iOS") {
                        window.location = ${LinkIPhone};
                    } else if (os == 'Android') {
                        window.location = ${LinkAndroid};
                    } else {
                        window.location = "https://kizuner.com";
                    }
                };
            </script>
        </body>
        </html>
    `;
};

app.get("/k", (req, res) => {
    const { t, d, i, k, id } = req.query;

    const title = decodeURIComponent(t) || "Do What You Love - Kizuner";
    const description = decodeURIComponent(d);
    const image_id = decodeURIComponent(i);
    const image_url =
        image_id && image_id !== "undefined"
            ? `https://storage.googleapis.com/kizuner-storage-live/${image_id}`
            : "https://kizuner.com/wp-content/uploads/2020/07/Untitled-1.jpg";

    return res.send(
        buildShareContent({
            title,
            description,
            image_url
        })
    );
});

/**
 * DEPRECATED
 */
app.get("/dynamic-link", (req, res) => {
    const { deeplink, title, description, image_url } = req.query;

    return res.send(
        buildShareContent({
            dynamicLink: decodeURIComponent(deeplink),
            title: title || "Do What You Love - Kizuner",
            description: description || "",
            image_url:
                image_url && image_url !== "undefined"
                    ? image_url
                    : "https://kizuner.com/wp-content/uploads/2020/07/Untitled-1.jpg"
        })
    );
});

app.listen(port, () => {
    console.log(`Started at ${port}`);
});
