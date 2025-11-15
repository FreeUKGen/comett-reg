<?php $session = session(); ?>
    <section class="breadcrumb-trail my-4 mx-4" aria-label="breadcrumb">
        <a href="#" class="text-md">Home</a>
        <img
            src="/images/icon-breadcrumb-arrow.svg"
            alt="new item" />
        <a href="#">Support</a>
        <img
            src="/images/icon-breadcrumb-arrow.svg"
            alt="next item" />
        <span>Post Comment</span>
    </section>
    <div class="mx-4">
        <h1 class="pb-3">Post Comment</h1>
        <form action="/issue/create" method="post" id="reg-comment-form" class="issue comment-form--content container px-3 py-2 b" aria-label="post comment form">
            <p class="container mb-1">To report a problem, please fill in this form.</p>
            <div class="container my-2">
                <label for="subject" class="mb-1">Subject</label>
                <input type="text" id="subject" name="subject" value="<?php echo $session->subject1; ?>" placeholder="Enter a brief description">
            </div>
            <div class="container my-2">
                <label for="body" class="mb-1">Description of problem or suggestion</label>
                <textarea id="body" name="body" rows="7" value="<?php echo $session->body; ?>" placeholder="Describe your problem or suggestion here"></textarea>
            </div>
            <div class="container my-2">
                <label for="comment-type" class="mb-1">Select a comment type</label>
                <select id="comment-type" class="container mb-1" aria-label="select comment type">
                    <option selected>Question</option>
                    <option value="1">Item 1</option>
                    <option value="2">Item 2</option>
                    <option value="3">Item 3</option>
                    <option value="4">Item 4</option>
                    <option value="5">Item 5</option>
                </select>
            </div>
            <button type="submit" class="my-2">Register report</button>
        </form>
    </div>
