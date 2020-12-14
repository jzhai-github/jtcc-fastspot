/* --------------------------------------------------------------------------
	Module
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {
	let $Pagination = document.querySelectorAll('.js-pagination-via-url');

	if (!$Pagination.length) return;

	let module = function ($Element) {
		let state = {
			$Element,
			$Form: $Element.querySelector('form')
		};

		state.$Select = state.$Form.querySelector('select');

		return {
			onSubmit(event) {
				try {
					event.preventDefault();

					let location = state.$Select.value;

					window.location = location;
				} catch (error) {
					return true;
				}
			},
			bindUI() {
				state.$Form.addEventListener(
					'submit',
					this.onSubmit.bind(this)
				);
			},
			init() {
				this.bindUI();
			}
		};
	};

	Site.OnInit.push(function () {
		$Pagination.forEach(($Element) => {
			module($Element).init();
		});
	});
})(jQuery, Site);
