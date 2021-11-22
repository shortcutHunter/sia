(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalDetailKhs', modalDetailKhs);

  /* @ngInject */
  function modalDetailKhs() {
    //Usage:
    //<div modal-preview></div>
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/detail_khs',
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

        scope.total_nilai_absolut = 0;
        scope.total_nilai = 0;

        $.each(scope.data.khs_detail, function(i, v){
          scope.total_nilai_absolut += v.nilai_absolut;
          scope.total_nilai += (v.mata_kuliah.sks * v.nilai_mutu);
        });

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }
    }
  }
})();
