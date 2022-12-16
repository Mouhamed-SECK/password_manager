
window.onload = () => {

    const url = window.location.origin;

    const incorrectPassword = document.querySelector('#incorrectPassword');
    const PasswordForm = document.querySelector('#passwordForm');
    const decryptedData = document.querySelector('#decryptedPassword');



    
    
    let privateKey;
    let passwordId;

    showPasswordBtns=  document.querySelectorAll('.showpassword')
    showPasswordBtns.forEach(btn => {
        btn.addEventListener('click', function handleClick(event) {
            privateKey = btn.getAttribute('data-privateKey').toString().trim()
            passwordId = btn.getAttribute('data-passwordId')

    
        });
      });
    PasswordForm.addEventListener("submit", onSubmit);

    async function onSubmit(event) {

        const password = PasswordForm.elements['userPassword'].value;
        const passwordINput = PasswordForm.elements['userPassword'];



        event.preventDefault();

    

            let  result = await axios.post(url + '/users/verify', {password});
    
            if (result.data.isCorrectPassword) {
               incorrectPassword.innerHTML = ""
               decryptedData.style.display = "block";


               console.log(passwordId)



              result = await axios.post(url + '/password/getPassword', {passwordId});
   
                const key = decryptData(password, privateKey); 
               // const Realpassword  = decryptData(key, result.data.password);


                  //console.log(key)
                  //console.log(Realpassword)




            } else {
                incorrectPassword.innerHTML = "Mot de Pass incorrecty"

            }

          
          
    
   
    }
  

   
}
