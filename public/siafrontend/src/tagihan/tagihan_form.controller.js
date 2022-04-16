(function() {
  'use strict';

  angular
    .module('app.tagihan')
    .controller('TagihanFormController', TagihanFormController);

  TagihanFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$scope', '$element'];
  /* @ngInject */
  function TagihanFormController($q, dataservice, logger, stateParams, compile, scope, element) {
    var vm = this;
    vm.title = 'Form Tagihan';
    vm.table = 'tagihan';
    vm.data = {};

    vm.tambahPembiayaan = tambahPembiayaan;
    vm.removeData       = removeData;

    scope.$watch('vm.data.tagihan_item', itemChanges, true);

    activate();

    function activate() {
      if (stateParams.dataId) {
        var promises = [getDataDetail()];
        return $q.all(promises).then(function() {
          
        });
      }
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function tambahPembiayaan() {
      let el = "<modal-pembiayaan-tagihan ng-model='vm.data.tagihan_item'></modal-pembiayaan-tagihan>";
      el = compile(el)(scope);
      $(element).append(el);
    }

    function itemChanges(newVal, oldVal) {
      let total_nominal = 0;
      if (newVal) {
        $.each(newVal, function(i, v){
          total_nominal += parseInt(v.nominal);
        });
      }
      vm.data.nominal = total_nominal;
    }

    function removeData(indx) {
      vm.data.tagihan_item.splice(indx, 1);
    }
  }
})();
