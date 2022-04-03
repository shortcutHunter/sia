(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalPreview', modalPreview);

  /* @ngInject */
  modalPreview.$inject = ['$timeout', 'dataservice'];

  function modalPreview($timeout, dataservice) {
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
        /*
          if code is queued using $timeout, it should run after the DOM has been manipulated by Angular, 
          and after the browser renders (which may cause flicker in some cases)
          source: https://stackoverflow.com/a/17303759
        */
        $timeout(function() {
          $(element).modal('show');

          $(element).on('hidden.bs.modal', () => {
              element.remove();
          });

          if (scope.type == 'pdf') {
            // render pdf
            dataservice.getPdf(scope.base64).then(function(file){
              element.find('#pdf-container').append(file);
            });
          }
          
        }, 0);
      }
    }
  }
})();
