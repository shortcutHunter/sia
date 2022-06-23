(function() {
  'use strict';

  angular
    .module('app.pmb')
    .controller('PmbDetailController', PmbDetailController);

  PmbDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope', '$compile', '$element', '$state'];
  /* @ngInject */
  function PmbDetailController($q, dataservice, logger, stateParams, scope, compile, element, state) {
    var vm = this;
    vm.title = 'Detail PMB';
    vm.table = 'pmb';
    vm.data = {};
    vm.dataservice = dataservice;

    vm.updStatus = updStatus;

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
      return $q.all(promises).then(function() {
        
      });
    }

    scope.$watch('vm.data.dokumen_kesehatan', uploadDocument);

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

    function updStatus(status) {

      if (status.includes('gagal') || status.includes('tolak') || status.includes('pending')) {
        dataservice.postData(vm.table, {status: status}, vm.data.id).then(function(data){
          state.reload();
        });
      } else {
        let el = `<modal-isi-tanggal data='vm.data' status='${status}'></modal-isi-tanggal>`;
        el = compile(el)(scope);
        $(element).append(el);
      }
    }

    function uploadDocument(newVal, oldVal) {
      if (newVal && oldVal !== undefined) {
        return dataservice.postData(vm.table, {dokumen_kesehatan: newVal}, vm.data.id).then(function(data){
          state.reload();
        });
      }
    }

  }
})();
