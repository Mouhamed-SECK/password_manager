window.onload = () => {
  const url = window.location.origin;
  const UserForm = document.querySelector("#createUserForm");

  const incorrectPassword = document.querySelector("#incorrectPassword");

  let groupId = parseInt(document.querySelector("#group-id").value);

  UserForm.addEventListener("submit", onSubmit);

  async function onSubmit(event) {
    let password = document.getElementById("password").value;
    let email = document.getElementById("email").value;
    let firstname = document.getElementById("firstname").value;
    let lastname = document.getElementById("lastname").value;
    event.preventDefault();

    data = {
      email,
      firstname,
      lastname,
    };

    console.log(data);
    console.log(groupId);

    try {
      let result = await axios.post(url + "/admin/groups/verify", { password });

      if (result.data.isCorrectPassword) {
        console.log("it's okay");
        incorrectPassword.innerText = "";

        result = await axios.post(url + "/admin/groups/getGroupKey", {
          groupId,
        });
        groupKey = result.data.key;
        console.log("Encrypted group key with super admin password", groupKey);

        groupKey = decryptData(password, groupKey);

        console.log("decripted group key", groupKey);

        const tempPassword = generateSceureKey();
        data.tempPassword = tempPassword;

        data.privateKey = encryptData(groupKey, tempPassword);

        console.log(data);

        result = await axios.post(url + "/admin/users/createGroupUser", data);

        if (result.data.success) {
          window.location.reload();
        } else {
          incorrectPassword.innerHTML = "Cet utilisateur existe déjà";
        }
      } else {
        incorrectPassword.innerText = "Votre mot de pass est incorrect";
      }
    } catch (error) {
      if (error.response.status === 403) {
        window.location = "/login";
      }
    }
  }
};
