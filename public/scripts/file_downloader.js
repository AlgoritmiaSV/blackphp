function FileDownloader()
{
	document.querySelectorAll('.file_downloader').forEach(button => {
		button.addEventListener('click', () => {
			const fileUrl = button.getAttribute('data-href');
			const fileName = fileUrl.split('/').pop();

			const link = document.createElement('a');
			link.href = fileUrl;
			link.download = fileName;
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		});
	});
}
document.addEventListener('DOMContentLoaded', FileDownloader);
