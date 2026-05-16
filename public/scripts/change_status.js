function url_split() {
    let pathname = window.location.pathname;

    if (pathname.startsWith("/")) {
        pathname = pathname.substring(1);
    }
    if (pathname.endsWith("/")) {
        pathname = pathname.substring(0, pathname.length - 1);
    }

    const path = pathname.split("/");
    const url_object = {
        module: path[0],
        method: path[1],
        id: path[2],
        options: {}
    };

    if (path.length > 3) {
        for (let i = 2; i < path.length; i += 2) {
            url_object.options[path[i]] = path[i + 1];
        }
    }

    return url_object;
}

let url = url_split();
let deletionUrl = null;
let deletionNext = null;

function delete_button_click(e) {
    const button = e.target.closest("button.delete_button, a.delete_link, button.change_status_button");
    if (!button) return;

    deletionUrl = button.dataset.url;
    deletionNext = button.dataset.next;
    let confirmationTitle = button.dataset.confirmationTitle;
    let confirmationText = button.dataset.confirmationText;
    let confirmButtonText = button.dataset.confirmButtonText;
    let cancelButtonText = button.dataset.cancelButtonText;

    Swal.fire({
        title: confirmationTitle || "Confirm",
        text: confirmationText || "Are you sure you want to delete this record?",
        showCancelButton: true,
        confirmButtonText: confirmButtonText || "Yes",
        cancelButtonText: cancelButtonText || "No",
        icon: "warning"
    }).then((result) => {
        if (result.isConfirmed) {
            delete_entry();
        }
    });
}

// Event delegation: listen at document level
document.addEventListener("click", function(e) {
    if (e.target.closest(".delete_button, .delete_link")) {
        delete_button_click(e);
    }
});

function delete_entry() {
    fetch(deletionUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(url)   // send the parsed URL object
    })
    .then(response => response.json())
    .then(deletionData => {
        if (deletionData.deleted || deletionData.success || deletionData.changed) {
            Swal.fire({
                title: deletionData.title || "Success",
                text: deletionData.message || "Deleted successfully!",
                icon: "success",
                didClose: function() {
                    if (deletionNext) {
                        window.open(deletionNext, "_top");
                    }
                }
            });
        } else if (deletionData.message) {
            Swal.fire({
                title: deletionData.title || "Error",
                text: deletionData.message,
                icon: "error"
            });
        } else {
            Swal.fire({
                title: "Error",
                text: "Failed to delete.",
                icon: "error"
            });
        }
    })
    .catch(() => {
        Swal.fire({
            title: "Error",
            text: "Request failed.",
            icon: "error"
        });
    });
}
