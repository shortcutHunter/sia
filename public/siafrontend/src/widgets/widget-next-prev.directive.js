(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetNextPrev', widgetNextPrev);

    widgetNextPrev.$inject = ['$compile', 'dataservice'];

    /* @ngInject */
    function widgetNextPrev(compile, dataservice) {
        var directive = {
            restrict: 'A',
            link: link
        };
        return directive;

        function link(scope, element, attrs)
        {
            element.find('[next]').on('click', () => {
                alert("Next");
            });

            element.find('[prev]').on('click', () => {
                alert("Prev");
            });
        }
    }

})();