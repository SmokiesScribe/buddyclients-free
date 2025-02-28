/**
 * Dismisses an admin notice.
 * 
 * @since 1.0.27
 */
document.addEventListener("DOMContentLoaded", function () {
    const dismissButtons = document.querySelectorAll(".buddyc-dismiss-admin-btn");

    dismissButtons.forEach(button => {
        const notice = button.closest(".notice");
        if ( ! notice ) return;

        const noticeId = notice.getAttribute("id") || notice.dataset.noticeId;
        if ( ! noticeId ) return;

        button.addEventListener("click", function (event) {
            event.preventDefault();

            // Hide
            notice.classList.add("dismissed");
            setTimeout(() => {
                notice.style.display = "none";
            }, 500);
            
            // Send to server to update option
            jQuery.post(ajaxurl, {
                action: 'buddyc_dismiss_admin_notice',
                noticeId: noticeId,
                nonce: dismissAdminData.nonce,
                nonceAction: dismissAdminData.nonceAction,
            })
        });
    });
});
