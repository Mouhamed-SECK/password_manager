window.onload = () => {

    let password = document.getElementById("password");
    let passwordConfirm = document.getElementById("password-confirm");
    let passwordTemp = document.getElementById("temp-password");
    let passwordError = document.querySelector(".message");
    let isSamePassword;
    const tempPasswordIncorrect = document.querySelector('#temp-password-incorrect');

    
    const userData = document.querySelector("#user-id");
    let userId = parseInt(userData.value);

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
                let result = await axios.post(url + '/users/verify', {password:passwordTemp.value});

                console.log(userId);

                if (result.data.isCorrectPassword) {
                    
                    tempPasswordIncorrect.innerHTML = ""

                    result = await  axios.post(url + '/users/getPrivateKey', {userId}) 

                    let userPrivatekey = result.data.key;
                    console.log("Encrypted user private key with temporary password", userPrivatekey)

                
                    const val = passwordTemp.value.trim();
        
                    userPrivatekey = decryptData(val,userPrivatekey);
                    console.log("mmm")
                    console.log("decrypted user private key from temporary password ", userPrivatekey)

                    const data = {
                        password : password.value,
                        userPrivatekey,
                        userId
                    }
                   
                 
                    data.userPrivatekey = encryptData(data.password, userPrivatekey);

                    console.log("decrypted user private key from temporary password ", data.userPrivatekey)

                    console.log(data)
                    

                    result = await axios.post(url + '/users/changeUserTempPassword', data);

                    if (result.data.success) {
                        window.location = '/logout'
                
                    }

                }else{
                    tempPasswordIncorrect.innerHTML = "Mot de passe temporaire incorrect";
                }
            }
          
        } catch (error) {
      
        }
    }
}
