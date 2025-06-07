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

		$('#add').click(function () {
			if (ValidateForm() > 0) return;

			const platform = $('#platform').val();
			const url = $('#url').val();
			const buttonText = $('#buttonText').val() || 'Default';

			// Voeg rij toe
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

			// Verwijder placeholderrij als die bestaat
			$('#overview .no-records-found').remove();

			// Heractiveer drag en update JSON + sequenties
			activateDnD();
			SetSequence();
		});

		$('#overview').on('click', '.editBtn', function () {
			const $row = $(this).closest('tr');
			const values = [];

			$row.find('td').not('.nodrop').each(function () {
				values.push($(this).text().trim());
			});

			alert("Celwaarden: " + values.join(", "));
		});

		$('#overview').on('click', '.deleteBtn', function () {

			$(this).closest('tr').remove();

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
			}, 0); // Uitgesteld tot na DOM update

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

			var errors = 0;

			if ($('#platform :selected').val() === '0') {
				$('#platform').addClass('is-invalid');
				errors++;
			}

			if ($('#url').val() === '') {
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
