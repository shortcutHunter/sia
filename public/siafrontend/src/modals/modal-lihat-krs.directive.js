(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalLihatKrs', modalLihatKrs);

  modalLihatKrs.$inject = ['$compile', 'dataservice'];

  /* @ngInject */
  function modalLihatKrs(compile, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/lihat_krs',
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
        getData();

        $(element).on('hidden.bs.modal', () => {
            element.remove();
            // scope.destroy();
        });
      }

      function getData() {
        let mahasiwa = scope.data.mahasiswa;
        let url = `/krs/${mahasiwa.id}/semester/${scope.data.mahasiswa.semester_id}`;
        dataservice.getUrl(url).then(function(response){
          scope.pengajuan_ks_detail = response.data;
        });
      }
    }
  }
})();
