(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalBuatTagihan', modalBuatTagihan);

  modalBuatTagihan.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalBuatTagihan(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/buat_tagihan',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;
      activate();

      scope.buatTagihan = buatTagihan;


      function activate() {
        $(element).modal('show');

        getData();

        $(element).on('hidden.bs.modal', () => {
            element.remove();
        });
      }

      function buatTagihan() {
        let url = "/buat/tagihan/mahasiswa";
        let data = {
          tahun_ajaran_id: scope.data.id
        };

        dataservice.postDataUrl(url, data).then(function(data){
          $(element).modal('hide');
          state.reload();
        });
      }

      function getData() {
        let filter = `tahun_ajaran_id=${scope.data.id}`;
        dataservice.getDataFilter('mahasiswa', filter).then(function(response){
          scope.data.mahasiswa = response.data;
        });
      }
    }
  }
})();
