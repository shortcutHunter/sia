(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetSubmit', widgetSubmit);

  widgetSubmit.$inject = ['dataservice', '$location', '$state', '$parse', 'logger'];

  /* @ngInject */
  function widgetSubmit(dataservice, location, state, parse, logger) {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      element.on('click', function(){
        let vm = scope.vm;
        let current_state = state.router.globals.current;
        let state_name = current_state.name;
        let is_valid = true;

        state_name = state_name.split("_form").shift();
        state_name = state_name.split("_detail").shift();

        $('[required]').each(function(i, v){
          if (['text', 'textarea', 'select-one'].includes(v.type)) {
            if (v.value) {
              $(v).removeClass('invalid');
            }else{
              is_valid = false;
              $(v).addClass('invalid');
            }
          }else if (['checkbox', 'radio'].includes(v.type)) {
            // if checkbox/radio have name
            if (v.name) {
              var el = $('[name="'+v.name+'"]');
              var is_checked = $('[name="'+v.name+'"]:checked');
              if (is_checked.length > 0) {
                el.removeClass('invalid');
              }else{
                is_valid = false;
                el.addClass('invalid');
              }
            // checkbox/radio have no name meaning single checkbox/radio
            }else{
              if (v.checked) {
                $(v).removeClass('invalid');
              }else{
                is_valid = false;
                $(v).addClass('invalid');
              }
            }
          }else if (v.type == 'file') {
            var value = parse('data.'+ v.name +'')(vm);
            if (value) {
              $(v).parent().removeClass('invalid');
            }else{
              is_valid = false;
              $(v).parent().addClass('invalid');
            }
          }
        });

        if (is_valid) {
          dataservice.postData(vm.table, vm.data, vm.data.id).then(function(data){
            location.path(`/${state_name}/${data.id}`);
          });
        }else{
          logger.error('Mohon isi semua kotak yang berwarna merah');
        }

      });
    }
  }
})();
