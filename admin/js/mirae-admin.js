(function ($) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $(window).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practice to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practicing this, we should strive to set a better example in our own work.
	 */
	$(document).ready(function () {
		InitializeTable();
		SetSequence();
		LoadPlatformDropdown();

		// $('#save').on('click', function (e) {
		// 	const rawData = $('#userdata').val().trim();

		// 	try {
		// 		const parsed = JSON.parse(rawData);

		// 		if (!Array.isArray(parsed) || parsed.length === 0) {
		// 		throw new Error('Invalid or empty data');
		// 		}

		// 		// Alles is ok → verberg fout, toon succes
		// 		$('#error-message').hide();
		// 		$('#save-message').fadeIn().delay(3000).fadeOut();

		// 	} catch (err) {
		// 		e.preventDefault(); // voorkom verzending
		// 		$('#save-message').hide();
		// 		$('#error-message').html('<p>Invalid or empty data — please check your links before saving.</p>').fadeIn().delay(4000).fadeOut();
		// 	}
		// });


		$('#add').click(function () {
			if (ValidateForm() > 0) {
				$('#error-message').fadeIn().delay(4000).fadeOut();
				return;
			}

			$('#error-message').hide();

			const platform = $('#platform').val();
			const url = $('#url').val();
			const buttonText = $('#buttonText').val() || 'Default';

			$('#overview tbody').append(`
				<tr data-index="999" style="cursor: move;">
				<td></td>
				<td>${platform}</td>
				<td>${url}</td>
				<td>${buttonText}</td>
				<td class="nodrop">
					<a class="editBtn">edit</a> / <a class="deleteBtn">delete</a>
				</td>
				</tr>
			`);

			$('#overview .no-records-found').remove();

			activateDnD();
			SetSequence();

			$('#platform').val('');
			$('#url').val('');
			$('#buttonText').val('');
		});

		$('#overview').on('click', '.editBtn', function () {

			const $row = $(this).closest('tr');
			const $cells = $row.find('td');

			// Haal huidige waarden op
			const platform = $cells.eq(1).text().trim();
			const link = $cells.eq(2).text().trim();
			const buttonText = $cells.eq(3).text().trim();

			$row.data('original', {
				platform,
				link,
				buttonText
			});

			// Vervang cellen met inputvelden
			$cells.eq(1).text(platform); 
			$cells.eq(2).html(`<input type="url" class="regular-text link-input" value="${link}" />`);
			$cells.eq(3).html(`<input type="text" class="regular-text buttonText-input" value="${buttonText}" />`);

			// Wijzig knoppen
			$cells.eq(4).html(`<a class="saveBtn">save</a> / <a class="cancelBtn">cancel</a>`);
		});

		$('#overview').on('click', '.saveBtn', function () {
			const $row = $(this).closest('tr');
			const $cells = $row.find('td');

			// Haal waarden uit inputs
			const platform = $cells.eq(1).text().trim();
			const link = $row.find('.link-input').val().trim();
			
			let buttonText = $row.find('.buttonText-input').val().trim();
			if (buttonText === '') {
				buttonText = 'Default';
			}

			$cells.eq(1).text(platform);
			$cells.eq(2).text(link);
			$cells.eq(3).text(buttonText);

			$cells.eq(4).html(`<a class="editBtn">edit</a> / <a class="deleteBtn">delete</a>`);

			SetSequence();
		});

		$('#overview').on('click', '.cancelBtn', function () {
			const $row = $(this).closest('tr');
			const $cells = $row.find('td');
			const original = $row.data('original');

			if (!original) return;

			// Herstel originele waarden
			$cells.eq(1).text(original.platform);
			$cells.eq(2).text(original.link);
			$cells.eq(3).text(original.buttonText);

			// Herstel actieknoppen
			$cells.eq(4).html(`<a class="editBtn">edit</a> / <a class="deleteBtn">delete</a>`);
		});


		$('#overview').on('click', '.deleteBtn', function () {

			if (confirm('Are you sure you want to delete this link?')) {
				$(this).closest('tr').remove();
				SetSequence();
			}

			setTimeout(function () {
				const rows = document.getElementById('overview').tBodies[0].rows;
				const overviewValues = [];

				if (rows.length > 0) {
					for (let i = 0; i < rows.length; i++) {
						const seq = i + 1;
						rows[i].cells[0].innerText = seq;

						overviewValues.push({
							sequence: seq,
							platform: rows[i].cells[1].innerText.trim(),
							link: rows[i].cells[2].innerText.trim(),
							buttonText: rows[i].cells[3].innerText.trim(),
							editRemove: '<a class="editBtn">edit</a> / <a class="deleteBtn">delete</a>',
						});
					}
				}

				$('#userdata').val(JSON.stringify(overviewValues, null, 2));
			}, 0);
		});

		function LoadPlatformDropdown() {
			$.getJSON('https://mirae.dev/wp-content/plugins/mirae/admin/data/data.json', function (data) {
				const $dropdown = $('#platform');
				$dropdown.empty();

				$dropdown.append('<option value="">-- Kies een platform --</option>');

				$.each(data, function (key, item) {
					$dropdown.append(`<option value="${key}">${item.button}</option>`);
				});
			});
		}

		function ValidateForm() {
			$('#platform').removeClass('is-invalid');
			$('#url').removeClass('is-invalid');

			let errors = 0;
			const url = $('#url').val().trim();
			const platform = $('#platform :selected').val();

			if (platform === '0' || platform === '') {
				$('#platform').addClass('is-invalid');
				errors++;
			}

			if (url === '') {
				$('#url').addClass('is-invalid');
				errors++;
			} else if (!/^(https?:\/\/)/i.test(url)) {
				$('#url').addClass('is-invalid');
				errors++;
			}

			return errors;
		}

		function activateDnD() {
			setTimeout(function () {
				$('#overview').tableDnD({});
			}, 0);
		}

		function SetSequence() {
			const table = document.getElementById('overview');

			if (!table || !table.tBodies.length) return;

			const rows = table.tBodies[0].rows;
			const overviewValues = [];

			for (let i = 0; i < rows.length; i++) {
				if (rows[i].cells.length < 4) continue;

				const seq = i + 1;
				rows[i].cells[0].innerText = seq;

				overviewValues.push({
					sequence: seq,
					platform: rows[i].cells[1].innerText.trim(),
					link: rows[i].cells[2].innerText.trim(),
					buttonText: rows[i].cells[3].innerText.trim(),
					editRemove: '<a class="editBtn">edit</a> / <a class="deleteBtn">delete</a>',
				});
			}

			$('#userdata').val(overviewValues.length ? JSON.stringify(overviewValues, null, 2) : "");
		}

		function InitializeTable() {
			const data = JSON.parse($('#userdata').val() || '[]');
			const $tbody = $('#overview tbody');
			let originalIndex = 0;

			$tbody.empty();

			if (data.length === 0) {
				$tbody.append('<tr class="no-records-found"><td colspan="5">No data found</td></tr>');
				return;
			}

			data.forEach(item => {
				$tbody.append(`
					<tr data-index="${item.sequence}" style="cursor: move;">
						<td>${item.sequence}</td>
						<td>${item.platform}</td>
						<td>${item.link}</td>
						<td>${item.buttonText}</td>
						<td class="nodrop">
						<a class="editBtn">edit</a> / <a class="deleteBtn">delete</a>
						</td>
					</tr>
				`);
			});

			activateDnD();

			$tbody.on('mousedown', 'tr', function () {
				originalIndex = $(this).index();
			});

			$tbody.on('mouseup', 'tr', function () {
				SetSequence();
			});
		}
	});
})(jQuery);
