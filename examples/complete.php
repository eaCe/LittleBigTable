<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <title>'littleBIGtable' Complete example</title>
    <meta name="description" content="littleBIGtable is a small (~4k gzipped) javascript table using AlpineJS"/>
    <style type="text/tailwindcss">
        @layer utilities {
            pre {
                @apply !text-white;
            }


            button.sort {
                @apply p-1;
            }

            button.sort .icon {
                @apply w-4 h-4;
            }
        }
    </style>
</head>
<body>
<section class="section py-10 ">
    <div class="container mx-auto max-w-6xl prose">
        <h1>littleBIGtable</h1>
        <p class="text-xl">
            This is the complete example with custom settings
        </p>
        <p>
            Passing in custom options is easy, just create an options object with the properties you want
            to override and pass it through the <code>x-data</code> attribute
        </p>
        <pre><code>let options = {
    // set this to wherever your data comes from
    url: 'http://localhost:8080/examples/json.php',
    // this is the prefix for the page limit preference, you can may want to change this on a per table basis
    keyPrefix: 'lbt',
    // change the default status bar messages as you see fit
    messages: {
        loading: 'Loading...',
        failed: 'Loading failed',
        summary: 'rows'     // this may be null, 'rows' or 'pages'
    },
    // formatters change the way cell data is displayed
    formatters: {},
    // the location of the SVG icons file
    icon: '../dist/icons.svg',
}</code></pre>
        <p>
            Now pass in your options to the component
        </p>
        <pre class="mb-6"><code>&lt;div x-data="littleBIGtable(options)" x-init="init()"&gt;</code></pre>

        <div x-data="littleBIGtable(options)" x-init="init()">
            <div class="flex space-x-4 justify-between mb-8">
                <div>
                    <label class="font-bold mr-2" for="search">Search</label>
                    <input class="input" id="search" type="text" placeholder="Start typing..." x-model="params.search" @keyup.debounce.350="doSearch()">
                </div>
                <div>
                    <label class="font-bold mr-2" for="per_page">Show</label>
                    <select @change="setLimit()" x-model="params.limit" id="per_page">
                        <option value="10">10 per page</option>
                        <option value="15">15 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
            </div>
            
            
            <table class="table-auto w-full">
                <thead>
                <tr>
                    <th>State
                        <button class="sort" type="button" x-html="getSortIcon('state')" @click="doSort('state')"></button>
                    </th>
                    <th>County
                        <button class="sort" type="button" x-html="getSortIcon('county')" @click="doSort('county')"></button>
                    </th>
                    <th>
                        Year
                        <button class="sort" type="button" x-html="getSortIcon('year')" @click="doSort('year')"></button>
                    </th>
                    <th>
                        Capacity
                        <button class="sort" type="button" x-html="getSortIcon('turbine_capacity')" @click="doSort('turbine_capacity')"></button>
                    </th>
                    <th>Turbines
                        <button class="sort" type="button" x-html="getSortIcon('project_capacity')" @click="doSort('project_capacity')"></button>
                    </th>
                </tr>
                </thead>
                <tfoot class="table-header-group">
                    <tr>
                        <th class="py-2">
                            <label class="block font-normal">
                                <input class="input p-1" name="state" type="text" placeholder="Start typing..." @keyup.debounce.350="doColumnSearch($el)">
                            </label>
                        </th>
                        <th>
                            <label class="block font-normal">
                                <input class="input p-1" name="county" type="text" placeholder="Start typing..." @keyup.debounce.350="doColumnSearch($el)">
                            </label>
                        </th>
                        <th>
                            <label class="block font-normal">
                                <input class="input p-1" name="year" type="text" placeholder="Start typing..." @keyup.debounce.350="doColumnSearch($el)">
                            </label>
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
                <tbody>
                <template x-for="row in rows">
                    <tr @click="doRowClicked($event)" data-id="xyz">
                        <td x-html="row.state"></td>
                        <td x-text="row.county"></td>
                        <td x-text="row.year"></td>
                        <td x-html="row.turbine_capacity"></td>
                        <td x-text="row.project_capacity"></td>
                    </tr>
                </template>
                </tbody>
            </table>


            <div class="flex items-center justify-between">
                <div>
                    <p x-html="meta.status"></p>
                </div>
                <nav role="navigation" aria-label="pagination">
                    <ul class="flex list-none m-0 p-0">
                        <li>
                            <button class="inline-flex items-center justify-center rounded border px-3 py-2 disabled:cursor-not-allowed disabled:opacity-30" aria-label="Goto first page" @click="goFirstPage()" :disabled="getCurrentPage() == 1">
                                <svg class="w-6 h-6">
                                    <use xlink:href="../dist/icons.svg#page-first"></use>
                                </svg>
                            </button>
                        </li>
                        <li>
                            <button class="inline-flex items-center justify-center rounded border px-3 py-2 disabled:cursor-not-allowed disabled:opacity-30" @click="goPrevPage()" :disabled="getCurrentPage() == 1">
                                <svg class="w-6 h-6">
                                    <use xlink:href="../dist/icons.svg#page-prev"></use>
                                </svg>
                            </button>
                        </li>
                        <li>

                        </li>
                        <li>
                            <button class="inline-flex items-center justify-center rounded border px-3 py-2 disabled:cursor-not-allowed disabled:opacity-30" @click="goNextPage()" :disabled="getCurrentPage() == getTotalPages()">
                                <svg class="w-6 h-6">
                                    <use xlink:href="../dist/icons.svg#page-next"></use>
                                </svg>
                            </button>
                        </li>
                        <li>
                            <button class="inline-flex items-center justify-center rounded border px-3 py-2 disabled:cursor-not-allowed disabled:opacity-30" aria-label="Goto last page" @click="goLastPage()" :disabled="getCurrentPage() == getTotalPages()">
                                <svg class="w-6 h-6">
                                    <use xlink:href="../dist/icons.svg#page-last"></use>
                                </svg>
                            </button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</section>
<script src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
<script src="../dist/littleBIGtable.min.js"></script>
<script nonce="abc123">
  options = {
    'url': './json.php',
    'limit': 25,
    'formatters': {
      'state': function (value, row) {
        return '<strong>' + value + '</strong>';
      },
      'turbine_capacity': function (value, row) {
        if (parseInt(value) < 1500) {
          return '<span class="text-orange-500 font-medium">' + value + '</span>';
        }
        if (parseInt(value) > 2000) {
          return '<span class="text-green-500 font-medium">' + value + '</span>';
        }
        return '<span class="text-indigo-500 font-medium">' + value + '</span>';
      }
    }
  };
  // this is an example of a row click event, in this example the row is specified as follows:
  // <tr @click="doRowClicked($event)" data-id="xyz">
  function doRowClicked (e) {
    console.log('The row was clicked, below is the event and the data-id of the row');
    console.log(e);
    console.log(e.target.parentNode.attributes['data-id'].value);
  }
</script>
</body>
</html>
