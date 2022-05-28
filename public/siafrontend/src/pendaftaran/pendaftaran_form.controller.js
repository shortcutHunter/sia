(function() {
  'use strict';

  angular
    .module('app.pendaftaran')
    .controller('PendaftaranFormController', PendaftaranFormController);

  PendaftaranFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope'];
  /* @ngInject */
  function PendaftaranFormController($q, dataservice, logger, stateParams, scope) {
    var vm = this;
    vm.title = 'Form Pendaftaran';
    vm.table = 'pendaftaran';
    vm.data = {
      max_cicilan: 10,
      status: 'process'
    };

    activate();

    function activate() {
      var promises = [getOption()];
      if (stateParams.dataId) {
        promises.push(getDataDetail());
      }

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
        console.log(vm.option);
        vm.option = response;
      });
    }

  }
})();
