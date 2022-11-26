window.onload = () => {

    let password = document.getElementById("password");
    let passwordConfirm = document.getElementById("password-confirm");
    let passwordTemp = document.getElementById("temp-password");
    let passwordError = document.querySelector(".message");
    let isSamePassword;
    let userId = document.getElementById("user-id");

    passwordConfirm.addEventListener("keyup",onKeyup);
  
    const ResetForm = document.querySelector("#reset-password-form");

    console.log(ResetForm);

    ResetForm.addEventListener("submit", onSubmit);

    function onKeyup(){
        console.log(password.value , passwordConfirm.value)
        if(password.value != passwordConfirm.value) {
            isSamePassword = false;
            passwordError.innerHTML = "les mot de pass doivent etre identique" ;
        }  else {
            passwordError.innerHTML = "" ;
            isSamePassword = true;
        }
    }

    async function onSubmit(event) {
        event.preventDefault();
        const url = window.location.origin; 
     
        try {
            if(isSamePassword) {
                const result = await axios.post(url + '/users/verify', {password:passwordTemp.value});

                console.log(result);
                console.log(userId.value);

                if (result.data.isCorrectPassword) {
                    
                    groupKey = encryptData(passwordTemp, generateSceureKey());

                    data = {
                        groupKey,
                        title
                    }
    
                    await saveGoupe(url + '/save', data); 
                    window.location.reload()  ;  
                }
            }
          
        } catch (error) {
      
        }
    }
}
