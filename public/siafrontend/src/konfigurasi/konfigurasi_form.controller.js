(function() {
  'use strict';

  angular
    .module('app.konfigurasi')
    .controller('KonfigurasiFormController', KonfigurasiFormController);

  KonfigurasiFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$element', '$scope', '$state'];
  /* @ngInject */
  function KonfigurasiFormController($q, dataservice, logger, stateParams, compile, element, scope, state) {
    var vm = this;
    vm.title = 'Form Konfigurasi';
    vm.table = 'konfigurasi';
    vm.data = {};

    vm.submitForm = submitForm;

    activate();

    function activate() {
      var promises = [getOption(), getDataDetail()];
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
      });
    }

    function getDataDetail() {
      return dataservice.getUrl('/konfigurasi').then(function(response) {
        vm.data = response.data;
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

    function submitForm() {
      return dataservice.postData(vm.table, vm.data, vm.data.id).then(function(data){
        state.go('konfigurasi');
      });
    }
  }
})();
