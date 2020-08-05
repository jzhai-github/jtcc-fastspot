/* --------------------------------------------------------------------------
	Accordion
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function ($, Site) {
	var $Accordion, $AccordionContent, $AccordionSwap;

	function init() {
		$Accordion = $('.js-accordion');

		if (!$Accordion.length) {
			return;
		}

		$AccordionContent = $('.js-accordion-content');
		$AccordionSwap = $('.js-accordion-swap');

		$AccordionContent.attr('aria-hidden', 'true');
		$AccordionSwap.attr('aria-expanded', 'false');

		$Accordion.each(function () {
			if ($(this).find('.fs-swap-active').length) {
				$(this)
					.find('.js-accordion-content:eq(0)')
					.attr('aria-hidden', 'false');
				$(this)
					.find('.js-accordion-item:eq(0) .js-accordion-swap')
					.attr('aria-expanded', 'true');
			}
		});

		$AccordionSwap
			.on('activate.swap', onTriggerActivate)
			.on('deactivate.swap', onTriggerDeactivate)
			.on('keydown', onTriggerKeydown);
	}

	function activeFirstAccordion(accordion) {
		$(accordion)
			.find('.js-accordion-item:first-of-type .js-accordion-swap')
			.focus();
	}

	function activeLastAccordion(accordion) {
		$(accordion)
			.find('.js-accordion-item:last-of-type .js-accordion-swap')
			.focus();
	}

	function activeNextAccordion(accordion) {
		$(accordion).next().find('.js-accordion-swap').focus();
	}

	function activePrevAccordion(accordion) {
		$(accordion).prev().find('.js-accordion-swap').focus();
	}

	function onTriggerActivate() {
		$(this).attr('aria-expanded', 'true');

		$Accordion
			.find($(this).data('swap-target') + ' .js-accordion-content')
			.attr('aria-hidden', 'false');
	}

	function onTriggerDeactivate() {
		$(this).attr('aria-expanded', 'false');

		$Accordion
			.find($(this).data('swap-target') + ' .js-accordion-content')
			.attr('aria-hidden', 'true');
	}

	function onTriggerKeydown(e) {
		if ([36, 35, 38, 40].indexOf(e.keyCode) === -1) {
			return;
		}

		var $focusedElement = $(':focus'),
			$focusedItem = $focusedElement.closest('.js-accordion-item'),
			$closestAccordion = $(this).closest('.js-accordion');

		e.preventDefault();

		switch (e.keyCode) {
			// home
			case 36:
				activeFirstAccordion($closestAccordion);

				break;
			// end
			case 35:
				activeLastAccordion($closestAccordion);

				break;
			// up
			case 38:
				if ($focusedItem.prev().length > 0) {
					activePrevAccordion($focusedItem);
				} else {
					activeLastAccordion($closestAccordion);
				}

				break;
			// down
			case 40:
				if ($focusedItem.next().length > 0) {
					activeNextAccordion($focusedItem);
				} else {
					activeFirstAccordion($closestAccordion);
				}

				break;
		}
	}

	Site.OnInit.push(init);
})(jQuery, Site);
