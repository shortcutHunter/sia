(function() {
  'use strict';

  angular
    .module('app.pmb')
    .controller('PmbController', PmbController);

  PmbController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function PmbController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'PMB';
    vm.table = 'pmb';
    vm.page = 0;
    vm.data = [];
    vm.pageData = {};
    vm.searchValue = false;

    vm.search = search;
    vm.updStatus = updStatus;

    activate();

    scope.$watch('vm.page', function(newVal){
      if (newVal != 0) {
        getData();
      }
    });

    function activate() {
      vm.page = 1;
    }

    function getData() {
      return dataservice.getData(vm.table, vm.page, vm.searchValue).then(function(response) {
        vm.pageData = response;
        vm.data = response.data;
      });
    }

    function search() {
      vm.searchValue = {
        'field': 'orang',
        'value': angular.copy(vm.searchData)
      };
      if (vm.page == 1) {
        getData();
      }else{
        vm.page = 1;
      }
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

    scope.$watch('vm.searchData', function(newVal, oldVal) {
      if (newVal != undefined) {
        search();
      }
    });

  }
})();
