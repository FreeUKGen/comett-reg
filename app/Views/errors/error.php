<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Comment page testing myspace2</title>

    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">

    <!-- css -->
    <link rel="stylesheet" href="<?= base_url('css/fc-reg.css') ?>">


    <!-- button href needs to be confirmed -->

    <style>
        :root {
            --font-body: "Source Sans 3", sans-serif;
            --fs-1: var(--size-1);
            --ls-1: 4%;
            --fw-normal: 400;
            --fw-heading: 600;
            --line-height: 2;
        }

        /* Base typography */

        html {
            height: 100%;
        }

        body {
            font-family: var(--font-body);
            font-size: var(--fs-1);
            letter-spacing: var(--ls-1);
            font-weight: var(--fw-normal);
            height: 100%;
            margin: 0;
        }

        /* Breadcrumb trail */
        .breadcrumb-trail img {
            margin-inline: 6px;
        }

        .breadcrumb-trail a {
            text-decoration: none;
        }

        .container-fluid {
            min-height: 100vh;
            display: flex;
            flex-direction: column;

        }

        .error-wrapper {
            flex: 1;

        }

        h1 {
            font-weight: var(--fw-heading);
        }

        /* Error message box */

        .error-message {
            text-align: center;
            line-height: var(--line-height);

            font-size: var(--fs-1);
            font-weight: var(--fw-normal);
        }

        /* Button  */

        .btn-wrapper {
            display: grid;
            justify-content: center;
        }

        .btn-error-home {
            border: none;
            font-size: var(--fs-1);
        }
    </style>


</head>

<body>
    <section class="breadcrumb-trail my-4 mx-4" aria-label="breadcrumb">
        <a href="#" class="text-md">Home</a>
        <img
            src="./css/images/icon-breadcrumb-arrow.svg"
            alt="breadcrumb trail next item indicator" />
        <span>Page not found</span>
    </section>
    <div class="mx-4 error-wrapper">
        <h1 class="pb-3 mb-2">Page not found</h1>
        <div class="px-3 py-3 mb-4 error-wrapper">
            <p class="error-message my-3">Sorry, the page you are looking for does not exist.<br> If you believe this is an error, please reach out to your coordinator for assistance.</p>
            <div class="my-3 btn-wrapper">
                <button type="button" class="btn-error-home"
                    onclick="window.location.href= '<?= base_url() ?>' ">
                    Go to Home
                </button>

            </div>

        </div>
    </div>

</body>

</html>