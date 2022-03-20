(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalLihatKhs', modalLihatKhs);

  modalLihatKhs.$inject = ['$compile'];

  /* @ngInject */
  function modalLihatKhs(compile) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/lihat_khs',
      scope: {
        data: '='
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      activate();

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      scope.detailKhs = function(data) {
        scope.active_data = data;
        let el = "<modal-detail-khs data='active_data'></modal-detail-khs>";
        el = compile(el)(scope);
      }
    }
  }
})();
