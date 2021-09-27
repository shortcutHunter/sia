(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalPreview', modalPreview);

  /* @ngInject */
  function modalPreview() {
    //Usage:
    //<div modal-preview></div>
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/preview',
      scope: {
        file: '=',
        name: '=',
        type: '=',
        mimetype: '=',
        base64: '='
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      activate();

      function activate() {
        $(element).modal('show');
      }
    }
  }
})();
