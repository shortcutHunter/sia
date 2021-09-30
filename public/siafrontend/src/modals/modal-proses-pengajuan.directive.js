(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalProsesPengajuan', modalProsesPengajuan);

  modalProsesPengajuan.$inject = ['$state', 'dataservice', '$compile'];

  /* @ngInject */
  function modalProsesPengajuan(state, dataservice, compile) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/proses_pengajuan',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;

      scope.tolak = tolak;
      scope.terima = terima;
      scope.closeModal = closeModal;

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
        let filter = `mahasiswa_id=${mahasiwa.id}`;
        filter = `${filter}&status=proses`;
        dataservice.getDataFilter('pengajuan_ks', filter).then(function(response){
          scope.pengajuan_ks = response.data[0];
        });
      }

      function tolak() {
        let el = "<modal-tolak-pengajuan data='data'></modal-tolak-pengajuan>";
        el = compile(el)(scope);
      }

      function terima() {
        let data = {status: 'terima'};
        dataservice.postData('pengajuan_ks', data, scope.pengajuan_ks.id).then(function(response){
          closeModal();
          state.reload();
        });
      }

      function closeModal() {
        $(element).modal('hide');
      }

    }
  }
})();
