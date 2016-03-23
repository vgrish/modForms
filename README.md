## ModForms

```
[[!mf.form?
    &tplForm=`mf.form`
    &tplModal=`mf.modal`
	&selector=`.mfform`
	&objectName=`ModFormsForm`
	&validation=`{
	    "rules":{
	        'name': {
                "required": true
            },
            "email": {
                "email": true,
                "required": true
            },
            "phone": {
                "required": true,
                "customphone": true
            }
	    },
	    "messages": {
            "name": {
                "required": "Пожалуйста введите имя"
            },
            "email": {
                "required": "Пожалуйста введите email",
                "email": "Пожалуйста укажите правильный email"
            },
            "phone": {
                "required": "Пожалуйста укажите телефон",
                "customphone": "Пожалуйста укажите правильный телефон"
            }
        },
        "error": {
            "label": true
        }
	}`

	&inputmask=`{
	    "phone": {
	        "mask":"+7(999)999-99-99",
	        "showMaskOnHover": false
	    }
	}`

	&modal=`{"center":true}`
]]

```