(function() {
  'use strict';

  angular
    .module('app.pmb')
    .controller('PmbDetailController', PmbDetailController);

  PmbDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope', '$compile', '$element'];
  /* @ngInject */
  function PmbDetailController($q, dataservice, logger, stateParams, scope, compile, element) {
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

      if (status.includes('gagal')) {
        dataservice.postData(vm.table, {status: status}, vm.data.id).then(function(data){
          state.reload();
        });
      } else {
        let el = `<modal-isi-tanggal data='vm.data' status='${status}'></modal-isi-tanggal>`;
        el = compile(el)(scope);
        $(element).append(el);
      }
    }

  }
})();
