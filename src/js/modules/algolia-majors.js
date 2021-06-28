/* --------------------------------------------------------------------------
	Algolia - Majors
-------------------------------------------------------------------------- */

/* globals jQuery, Site */

(function($, Site) {

    function init() {
        const searchClient = algoliasearch(window.algolia.app_id, window.algolia.search_key);

        const search = instantsearch({
            searchClient,
            indexName: 'majors',
            routing: {
                router: instantsearch.routers.history(),
                stateMapping: instantsearch.stateMappings.simple(),
            },
        });

        // Create the render function
        const createDataAttribtues = refinement =>
            Object.keys(refinement)
                .map(key => `data-${key}="${refinement[key]}"`)
                .join('');

        const renderListItem = item => `
                  ${item.refinements
            .map(
                refinement =>
                    `<button ${createDataAttribtues(refinement)}><strong>${refinement.label}</strong></button>`
            )
            .join('')}
                  `;

        const renderCurrentRefinements = (renderOptions, isFirstRender) => {
            const { items, refine, widgetParams } = renderOptions;

            widgetParams.container.innerHTML = (items.length > 0) ? `in ${items.map(renderListItem).join(',')}` : ``;

            [...widgetParams.container.querySelectorAll('button')].forEach(element => {
                element.addEventListener('click', event => {
                    const item = Object.keys(event.currentTarget.dataset).reduce(
                        (acc, key) => ({
                            ...acc,
                            [key]: event.currentTarget.dataset[key],
                        }),
                        {}
                    );

                    refine(item);
                });
            });
        };

        // Create the custom widget
        const customCurrentRefinements = instantsearch.connectors.connectCurrentRefinements(
            renderCurrentRefinements
        );

        search.addWidgets([
            instantsearch.widgets.analytics({
                pushFunction(formattedParameters, state, results) {
                    dataLayer.push({
                        'event': 'search',
                        'Search Query': state.query,
                        'Facet Parameters': formattedParameters,
                        'Number of Hits': results.nbHits,
                    });
                },
            }),

            instantsearch.widgets.stats({
                container: '#stats',
                templates: {
                    text: `
                          <span class="filter_results_label">{{#areHitsSorted}}
                            {{#hasNoSortedResults}}No relevant results{{/hasNoSortedResults}}
                            {{#hasOneSortedResults}}1 relevant result{{/hasOneSortedResults}}
                            {{#hasManySortedResults}}{{#helpers.formatNumber}}{{nbSortedHits}}{{/helpers.formatNumber}} relevant results{{/hasManySortedResults}}
                            sorted out of {{#helpers.formatNumber}}{{nbHits}}{{/helpers.formatNumber}}
                          {{/areHitsSorted}}
                          {{^areHitsSorted}}
                            {{#hasNoResults}}No results{{/hasNoResults}}
                            {{#hasOneResult}}1 result{{/hasOneResult}}
                            {{#hasManyResults}}{{#helpers.formatNumber}}{{nbHits}}{{/helpers.formatNumber}} results{{/hasManyResults}}
                          {{/areHitsSorted}}
                          {{ #query }}for {{ query }}{{ /query }}</span>
                        `,
                }
            }),

            customCurrentRefinements({
                container: document.querySelector('#current-refinements'),
            }),

            instantsearch.widgets.searchBox({
                container: '#major_search_input',
                placeholder: 'Search by keyword',
                showReset: false,
                showSubmit: false,
                templates: {
                },
                cssClasses: {
                    input: 'filter_search_input'
                }
            }),

            instantsearch.widgets.menuSelect({
                container: '#filter_degree_type',
                attribute: 'categories.degree_type.title',
                templates: {
                    defaultOption: 'All Degrees & Certificates',
                },
                cssClasses: {
                    select: 'filter_tool_select'
                }
            }),

            instantsearch.widgets.menuSelect({
                container: '#filter_career_cluster',
                attribute: 'categories.career_cluster.title',
                templates: {
                    defaultOption: 'All Career Clusters',
                },
                cssClasses: {
                    select: 'filter_tool_select'
                }
            }),

            instantsearch.widgets.menuSelect({
                container: '#filter_study_mode',
                attribute: 'categories.mode_of_study.title',
                templates: {
                    defaultOption: 'All Modes of Study',
                },
                cssClasses: {
                    select: 'filter_tool_select'
                }
            }),

            instantsearch.widgets.hits({
                container: '.program_list #item_list',
                escapeHTML: false,
                templates: {
                    item: `
                          <li class="js-accordion-item js-program-item-1-{{ __hitIndex }} program_item">
                            <div class="fs-row">
                              <div class="fs-cell">
                                <div class="program_item_inner">
                                  <h2 class="program_item_heading">
                                    <button class="js-accordion-swap js-swap program_item_button" data-swap-target=".js-program-item-1-{{ __hitIndex }}" data-swap-group="program-group-1">
                                      <span class="program_item_heading_inner">
                                        <span class="program_item_heading_label">
                                          <span class="program_item_heading_title">
                                            <span class="program_item_heading_title_label">{{ title }}</span>
                                              <span class="program_item_heading_title_icon">
                                                <svg class="icon icon_caret_right">
                                                  <use href="/images/icons.svg#caret_right" />
                                                </svg>
                                              </span>
                                            </span>

                                            <span class="program_item_heading_mode">
                                              <span class="program_item_heading_mode_label">Learning Mode<span class="program_item_heading_mode_label_hint">:													</span>
                                            </span>

                                            <span class="program_item_heading_mode_data_wrapper">
                                                {{#categories.mode_of_study}}
                                                    <span class="program_item_heading_mode_data theme_mustard">{{ title }}</span>
                                                {{/categories.mode_of_study}}
                                                {{#categories.program_type}}
                                                    <span class="program_item_heading_mode_data theme_green_blue">{{ title }}</span>
                                                {{/categories.program_type}}
                                            </span>
                                          </span>
                                        </span>
                                        <span class="program_item_heading_icon"></span>
                                      </span>
                                    </button>
                                  </h2>

                                  <div class="js-accordion-content program_item_content" aria-label="{{ title }}">
                                    <div class="program_item_content_inner">
                                      <div class="program_item_content_group">
                                        <div class="program_item_description typography">
                                          <p>{{ page_description }}</p>
                                        </div>
                                      </div>

                                      <div class="program_item_details">
                                        <div class="program_item_detail">
                                          <span class="program_item_detail_label">Degree & Certificate Type<span class="program_item_detail_label_hint">:</span></span>
                                          <span class="program_item_detail_data">
                                            {{#categories.degree_type}}
                                              <span class="program_item_detail_data_link_label">{{ title }}</span>
                                            {{/categories.degree_type}}
                                            <br /><br />
                                            <h3>
                                              <a class="program_item_detail_data_link" href="{{ url }}">
                                                <span class="program_item_detail_data_link_label">Major Details</span>
                                                  <span class="program_item_detail_data_link_icon">
                                                    <svg class="icon icon_caret_right">
                                                      <use href="/images/icons.svg#caret_right" />
                                                    </svg>
                                                  </span>
                                                </a>
                                              </h3>
                                            </span>
                                          </div>

                                          <div class="program_item_detail">
                                            <span class="program_item_detail_label">Career Cluster<span class="program_item_detail_label_hint">:</span></span>
                                            {{#categories.career_cluster}}
                                              <span class="program_item_detail_data">{{ title }}<br /></span>
                                            {{/categories.career_cluster}}
                                          </div>
                                        </div>

                                        {if link != ''}
                                          <div class="program_item_content_group">
                                            <div class="program_item_description typography">
                                              <h4><a href="{link}">More Information about {title} in Catalog</a></h4>
                                            </div>
                                          </div>
                                        {/if}
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </li>
                        `,
                    empty: ``,
                },
            })
        ]);

        search.start();

        search.on('render', () => {
            $('.js-accordion-algolia-list .js-swap').swap();
        });
    }

    Site.OnInit.push(init);

})(jQuery, Site);