window.onload = () => {
    const PasswordForm = document.querySelector("#passwordForm");
    const AdminpasswordForm = document.querySelector("#AdminpasswordForm");
    const adminData = document.querySelector("#adminData");


    let groupKey = document.getElementById("privateKey").value;

    const url = window.location.origin;

    let groupId = parseInt(adminData.value)
        

    const incorrectPassword = document.querySelector('#incorrectPassword');


    const adminPassordInput = document.querySelector('#adminPassword');


    AdminpasswordForm.addEventListener("submit", onSubmit)


     async function onSubmit(event) {


      event.preventDefault();

    
      const password = adminPassordInput.value;
     

        const title = PasswordForm.elements['title'].value;
        const usedUrl = PasswordForm.elements['url'].value;
        const login = PasswordForm.elements['login'].value;
        const email = PasswordForm.elements['email'].value;
        const createdPassword = PasswordForm.elements['password'].value;
        const description = PasswordForm.elements['description'].value;

        const data  = {
          title,
          usedUrl,
          login,
          email,
          createdPassword, 
          description
        }
      
       try {
            let  result = await axios.post(url + '/admin/groups/verify', {password});
            console.log(groupId)
        
            if (result.data.isCorrectPassword) {
               incorrectPassword.innerHTML = ""

             
          
               console.log("Encrypted group key with super admin password", groupKey)
   
               groupKey = decryptData(password, groupKey);

               data.createdPassword = encryptData(groupKey, data.createdPassword)

               console.log(data)

                result = await axios.post(url + '/admin/password/save', data);

                if (result.data.success) {
                  window.location.reload() ;     

              } 
               
            } else {
              incorrectPassword.innerHTML = "Mot de passe incorect"

            }

        } catch (error) {
            if (error.response.status === 403) {
                window.location = '/login'
            }
        }
          
    }

 

   
}
