import ModalComponent from '../../../_default/components/Modal.vue'
import SelectMaterialComponent from '../../../_default/components/SelectMaterial.vue'
import store from '../store/store'
import BillPay from '../models/bill-pay'

export default {
    components: {
        'modal': ModalComponent,
        'select-material': SelectMaterialComponent,
    },
    props: {
        index: {
            type: Number,
            required: false,
            'default': -1
        },
        modalOptions: {
            type: Object,
            required: true
        }
    },
    data(){
        return {
            bill: new BillPay(),
            bankAccount: {
                text: ''
            }
        }
    },
    computed: {
        cpOptions(){
            return {
                data: this.categoriesFormatted,
                templateResult(category){
                    let margin = '&nbsp'.repeat(category.level * 6)
                    let text = category.hasChildren ? `<strong>${category.text}</strong>` : category.text
                    return `${margin}${text}`
                },
                escapeMarkup(m){
                    return m
                }
            }
        },
        bankAccounts(){
            return store.state.bankAccount.lists
        },
        categoriesFormatted(){
            return store.getters[`${this.categoryNamespace()}/categoriesFormatted`]
        },
    },
    watch: {
        bankAccounts(bankAccounts){
            if (bankAccounts.length > 0){
                this.initAutoComplete()
            }
        }
    },
    methods:{
        doneId(){
            return `done-${this._uid}`
        },
        bankAccountTextId(){
            return `bank-account-text-${this._uid}`
        },
        bankAccountDropdownId(){
            return `bank-account-dropdown-${this._uid}`
        },
        bankAccountHiddenId(){
            return `bank-account-hidden-${this._uid}`
        },
        formId(){
            return `form-bill-${this._uid}`
        },
        repeatId(){
            return `repeat-${this._uid}`
        },
        blurBankAccount($event){
            let el = $($event.target)
            let text = this.bankAccount.text

            if (el.val() !== text){
                el.val(text)
            }
            this.validateBankAccount()
        },
        blurRepeatNumber($event){
            let el = $($event.target)
            let text = this.bankAccount.text

            if (parseInt(el.val(),10) < 0){
                el.val(0)
            }
            this.validateBankAccount()
        },
        validateCategory(){
            let valid = this.$validator.validate('category_id', this.bill.category_id)
            let parent = $(`#${this.formId()}`).find('[name="category_id"]').parent()
            let label = parent.find('label')
            let spanSelect2 = parent.find('.select2-selection.select2-selection--single')

            if (valid){
                label.removeClass('label-error')
                spanSelect2.removeClass('select2-invalid')
            }else{
                label.removeClass('label-error').addClass('label-error')
                spanSelect2.removeClass('select2-invalid').addClass('select2-invalid')
            }
        },
        validateBankAccount(){
            this.$validator.validate('bank_account_id', this.bill.bank_account_id)
        },
        initSelect2(){
            let self = this
            let select = $(`#${this.formId()}`).find('[name="category_id"]')

            select.on('select2:close', () => {
                self.validateCategory()
            })
        },
        initAutoComplete(){
            let self = this
            $(`#${this.bankAccountTextId()}`).materialize_autocomplete({
                limit: 10,
                multiple: {
                    enable: false
                },
                hidden: {
                    el: `#${this.bankAccountHiddenId()}`
                },
                dropdown: {
                    el: `#${this.bankAccountDropdownId()}`
                },
                getData(value,callback) {
                    let mapBankAccounts = store.getters['bankAccount/mapBankAccounts']
                    let bankAccounts = mapBankAccounts(value)
                    callback(value,bankAccounts)
                },
                onSelect(item){
                    self.bill.bank_account_id = item.id
                    self.bankAccount.text = item.text
                    self.validateBankAccount()
                }
            })
            $(`#${this.bankAccountTextId()}`).parent().find('label').insertAfter(`#${this.bankAccountTextId()}`)
        },
        submit(){
            this.validateCategory()
            this.$validator.validateAll().then(success => {
                if (success){
                    if (this.bill.id !== 0){
                        store.dispatch(`${this.namespace()}/edit`, {
                            bill: this.bill,
                            index: this.index
                        }).then(() => {
                            this.successSave('Conta atualizada com sucesso!')
                        })
                    }else{
                        store.dispatch(`${this.namespace()}/save`, this.bill).then(() => {
                            this.successSave('Conta criada com sucesso!')
                        })
                    }
                }
            })
        },
        successSave(message){
            $(`#${this.modalOptions.id}`).modal('close')
            Materialize.toast(message, 4000)
            this.resetScope()
        },
        resetScope(){
            this.errors.clear()
            this.fields.reset()
            this.bill.init()
            this.bankAccount = {
                text: ''
            }
        }
    },
}