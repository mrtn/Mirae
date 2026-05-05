(function ($) {
	'use strict';

	$(document).ready(function () {
		const data = (typeof miraeAdmin !== 'undefined' && miraeAdmin) ? miraeAdmin : { platforms: {}, i18n: {} };
		const i18n = data.i18n || {};
		const platforms = data.platforms || {};

		const t = (key, fallback) => (i18n[key] !== undefined ? i18n[key] : fallback);

		LoadPlatformDropdown();
		InitializeTable();
		SyncLinkData();

		$('.color-picker').wpColorPicker({
			defaultColor: false,
			change: function () {},
			clear: function () {}
		});

		$('#add').on('click', function () {
			if (ValidateForm() > 0) {
				$('#error-message').stop(true, true).fadeIn().delay(4000).fadeOut();
				return;
			}

			$('#error-message').hide();

			const platform = $('#platform').val();
			const url = $('#url').val();
			const buttonText = $('#buttonText').val() || 'Default';

			$('#overview tbody').append(buildRow(0, platform, url, buttonText));
			$('#overview .no-records-found').remove();

			activateDnD();
			SyncLinkData();

			$('#platform').val('');
			$('#url').val('');
			$('#buttonText').val('');
		});

		$('#overview').on('click', '.editBtn', function () {
			const $row = $(this).closest('tr');
			const $cells = $row.find('td');

			const platform = $cells.eq(1).text().trim();
			const link = $cells.eq(2).text().trim();
			const buttonText = $cells.eq(3).text().trim();

			$row.data('original', { platform, link, buttonText });

			$cells.eq(1).text(platform);
			$cells.eq(2).html(`<input type="url" class="regular-text link-input" value="${escapeHtml(link)}" />`);
			$cells.eq(3).html(`<input type="text" class="regular-text buttonText-input" value="${escapeHtml(buttonText)}" />`);
			$cells.eq(4).html(`<a class="saveBtn">${escapeHtml(t('save', 'save'))}</a> / <a class="cancelBtn">${escapeHtml(t('cancel', 'cancel'))}</a>`);
		});

		$('#overview').on('click', '.saveBtn', function () {
			const $row = $(this).closest('tr');
			const $cells = $row.find('td');

			const platform = $cells.eq(1).text().trim();
			const link = $row.find('.link-input').val().trim();
			let buttonText = $row.find('.buttonText-input').val().trim();
			if (buttonText === '') buttonText = 'Default';

			$cells.eq(1).text(platform);
			$cells.eq(2).text(link);
			$cells.eq(3).text(buttonText);
			$cells.eq(4).html(actionButtons());

			SyncLinkData();
		});

		$('#overview').on('click', '.cancelBtn', function () {
			const $row = $(this).closest('tr');
			const $cells = $row.find('td');
			const original = $row.data('original');

			if (!original) return;

			$cells.eq(1).text(original.platform);
			$cells.eq(2).text(original.link);
			$cells.eq(3).text(original.buttonText);
			$cells.eq(4).html(actionButtons());
		});

		$('#overview').on('click', '.deleteBtn', function () {
			if (!confirm(t('confirmDelete', 'Are you sure you want to delete this link?'))) return;

			$(this).closest('tr').remove();
			SyncLinkData();
		});

		function LoadPlatformDropdown() {
			const $dropdown = $('#platform');
			$dropdown.empty();
			$dropdown.append(`<option value="">${escapeHtml(t('selectPlatform', '-- Select a platform --'))}</option>`);

			Object.keys(platforms).forEach((key) => {
				$dropdown.append(`<option value="${escapeHtml(key)}">${escapeHtml(platforms[key])}</option>`);
			});
		}

		function ValidateForm() {
			$('#platform, #url').removeClass('is-invalid');

			let errors = 0;
			const url = $('#url').val().trim();
			const platform = $('#platform').val();

			if (!platform) {
				$('#platform').addClass('is-invalid');
				errors++;
			}

			if (!url || !/^https?:\/\//i.test(url)) {
				$('#url').addClass('is-invalid');
				errors++;
			}

			return errors;
		}

		function activateDnD() {
			setTimeout(function () {
				$('#overview').tableDnD({
					onDrop: function () {
						SyncLinkData();
					}
				});
			}, 0);
		}

		function SyncLinkData() {
			const table = document.getElementById('overview');
			if (!table || !table.tBodies.length) {
				$('#link_data').val('');
				return;
			}

			const rows = table.tBodies[0].rows;
			const out = [];

			for (let i = 0; i < rows.length; i++) {
				if (rows[i].classList.contains('no-records-found')) continue;
				if (rows[i].cells.length < 4) continue;

				const seq = out.length + 1;
				rows[i].cells[0].innerText = seq;

				out.push({
					sequence: seq,
					platform: rows[i].cells[1].innerText.trim(),
					link: rows[i].cells[2].innerText.trim(),
					buttonText: rows[i].cells[3].innerText.trim()
				});
			}

			$('#link_data').val(out.length ? JSON.stringify(out, null, 2) : '');
		}

		function InitializeTable() {
			let parsed = [];
			try { parsed = JSON.parse($('#link_data').val() || '[]'); } catch (e) { parsed = []; }

			const $tbody = $('#overview tbody');
			$tbody.empty();

			if (!parsed.length) {
				$tbody.append(`<tr class="no-records-found"><td colspan="5">${escapeHtml(t('noRecords', 'No data found'))}</td></tr>`);
				return;
			}

			parsed.forEach((item) => {
				$tbody.append(buildRow(item.sequence, item.platform, item.link, item.buttonText));
			});

			activateDnD();
		}

		function buildRow(seq, platform, link, buttonText) {
			return `
				<tr data-index="${seq}" style="cursor: move;">
					<td>${seq}</td>
					<td>${escapeHtml(platform)}</td>
					<td>${escapeHtml(link)}</td>
					<td>${escapeHtml(buttonText)}</td>
					<td class="nodrop">${actionButtons()}</td>
				</tr>
			`;
		}

		function actionButtons() {
			return `<a class="editBtn">${escapeHtml(t('edit', 'edit'))}</a> / <a class="deleteBtn">${escapeHtml(t('delete', 'delete'))}</a>`;
		}

		function escapeHtml(str) {
			return String(str == null ? '' : str)
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#39;');
		}

		function attachMediaUploader(inputId, buttonId, previewId) {
			let frame;
			$('#' + buttonId).on('click', function (e) {
				e.preventDefault();
				if (frame) { frame.open(); return; }

				frame = wp.media({
					title: t('mediaTitle', 'Select or upload an image'),
					button: { text: t('mediaButtonText', 'Use this image') },
					multiple: false
				});

				frame.on('select', function () {
					const attachment = frame.state().get('selection').first().toJSON();
					const imageUrl = attachment.url;
					const altText = t('previewAlt', 'Preview');

					$('#' + inputId).val(imageUrl);
					$('#' + previewId).html(`<img src="${escapeHtml(imageUrl)}" alt="${escapeHtml(altText)}" style="max-height:100px; border:1px solid #ccc;" />`);
				});

				frame.open();
			});
		}

		function hexToRgb(hex) {
			hex = String(hex || '').replace('#', '');
			if (hex.length === 3) hex = hex.split('').map((c) => c + c).join('');
			if (hex.length !== 6) return null;

			return {
				r: parseInt(hex.substring(0, 2), 16),
				g: parseInt(hex.substring(2, 4), 16),
				b: parseInt(hex.substring(4, 6), 16)
			};
		}

		function updatePreview() {
			const hex = $('#container_bg_color').val();
			const alpha = parseFloat($('#container_bg_alpha').val()) || 0.8;
			const rgb = hexToRgb(hex);
			if (rgb) {
				$('#container_preview').css('background-color', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${alpha})`);
			}
		}

		$('#container_bg_color, #container_bg_alpha').on('input change', updatePreview);

		attachMediaUploader('profile_picture', 'upload_profile_picture', 'profile_picture_preview');
		attachMediaUploader('background_image', 'upload_background_image', 'background_image_preview');
		attachMediaUploader('overlay_pattern', 'upload_overlay_pattern', 'overlay_pattern_preview');
	});
})(jQuery);
