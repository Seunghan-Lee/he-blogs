(function () {
  "use strict";

  if (typeof heblogsCommentActions === "undefined") {
    return;
  }

  var replyTemplateEl = document.getElementById("heblogs-inline-reply-template");
  var activeInlineReplyEl = null;
  var activeInlineReplyCommentId = "";

  function postAction(action, payload) {
    var params = new URLSearchParams();
    params.append("action", action);
    params.append("nonce", heblogsCommentActions.nonce);

    Object.keys(payload).forEach(function (key) {
      params.append(key, payload[key]);
    });

    return fetch(heblogsCommentActions.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
      },
      body: params.toString(),
    }).then(function (response) {
      return response.text().then(function (text) {
        try {
          return JSON.parse(text);
        } catch (e) {
          throw new Error(heblogsCommentActions.networkError);
        }
      });
    });
  }

  function closestCommentItem(el) {
    return el.closest("li[id^='comment-'], div[id^='comment-']");
  }

  function removeInlineReply() {
    if (activeInlineReplyEl) {
      activeInlineReplyEl.remove();
      activeInlineReplyEl = null;
      activeInlineReplyCommentId = "";
    }
  }

  function openInlineReply(commentItem, commentId) {
    if (!replyTemplateEl || !commentItem || !commentId) {
      return;
    }

    if (activeInlineReplyEl && activeInlineReplyCommentId === String(commentId)) {
      removeInlineReply();
      return;
    }

    removeInlineReply();

    var templateContent = replyTemplateEl.content
      ? replyTemplateEl.content.cloneNode(true)
      : null;
    if (!templateContent) {
      return;
    }

    var replyRoot = templateContent.querySelector(".comment-inline-reply");
    if (!replyRoot) {
      return;
    }

    var parentInput = replyRoot.querySelector("input[name='comment_parent']");
    if (parentInput) {
      parentInput.value = commentId;
    }

    var commentContent = commentItem.querySelector(".comment-content");
    if (!commentContent) {
      return;
    }

    commentContent.appendChild(replyRoot);
    activeInlineReplyEl = replyRoot;
    activeInlineReplyCommentId = String(commentId);

    var textarea = replyRoot.querySelector("textarea[name='comment']");
    if (textarea) {
      textarea.focus();
    }
  }

  function getDirectChildrenList(commentItem) {
    var node = commentItem.firstElementChild;
    while (node) {
      if (node.classList && node.classList.contains("children")) {
        return node;
      }
      node = node.nextElementSibling;
    }
    return null;
  }

  function toggleEditor(commentItem, open) {
    var contentEl = commentItem.querySelector(".js-comment-text");
    var editorEl = commentItem.querySelector(".js-comment-editor");
    var actionsEl = commentItem.querySelector(".comment-metadata-actions");
    if (!contentEl || !editorEl) {
      return;
    }

    if (open) {
      contentEl.hidden = true;
      editorEl.hidden = false;
      commentItem.classList.add("is-editing");
      if (actionsEl) {
        actionsEl.hidden = true;
      }
      var textarea = editorEl.querySelector(".js-comment-editor-textarea");
      if (textarea) {
        textarea.focus();
      }
      return;
    }

    contentEl.hidden = false;
    editorEl.hidden = true;
    commentItem.classList.remove("is-editing");
    if (actionsEl) {
      actionsEl.hidden = false;
    }
  }

  function requestPasswordIfRequired(trigger) {
    if (trigger.getAttribute("data-require-password") !== "1") {
      return "";
    }

    var password = window.prompt(heblogsCommentActions.passwordPrompt);
    if (password === null) {
      return null;
    }

    password = password.trim();
    if (!password) {
      window.alert(heblogsCommentActions.passwordNeeded);
      return null;
    }

    return password;
  }

  document.addEventListener("click", function (event) {
    var replyTrigger = event.target.closest(".js-inline-reply-trigger");
    if (replyTrigger) {
      event.preventDefault();

      var replyCommentItem = closestCommentItem(replyTrigger);
      if (!replyCommentItem) {
        return;
      }

      var replyCommentId = replyTrigger.getAttribute("data-comment-id");
      openInlineReply(replyCommentItem, replyCommentId);
      return;
    }

    var replyCancelTrigger = event.target.closest(".js-inline-reply-cancel");
    if (replyCancelTrigger) {
      event.preventDefault();
      removeInlineReply();
      return;
    }

    var editTrigger = event.target.closest(".js-comment-edit-trigger");
    if (editTrigger) {
      event.preventDefault();
      var editCommentItem = closestCommentItem(editTrigger);
      if (editCommentItem) {
        removeInlineReply();
        toggleEditor(editCommentItem, true);
      }
      return;
    }

    var cancelTrigger = event.target.closest(".js-comment-edit-cancel");
    if (cancelTrigger) {
      event.preventDefault();
      var cancelCommentItem = closestCommentItem(cancelTrigger);
      if (cancelCommentItem) {
        toggleEditor(cancelCommentItem, false);
      }
      return;
    }

    var saveTrigger = event.target.closest(".js-comment-edit-save");
    if (saveTrigger) {
      event.preventDefault();

      var saveCommentItem = closestCommentItem(saveTrigger);
      if (!saveCommentItem) {
        return;
      }

      var commentId = saveTrigger.getAttribute("data-comment-id");
      var textarea = saveCommentItem.querySelector(".js-comment-editor-textarea");
      var contentEl = saveCommentItem.querySelector(".js-comment-text");
      if (!commentId || !textarea || !contentEl) {
        return;
      }

      var content = textarea.value.trim();
      if (!content) {
        window.alert(heblogsCommentActions.emptyComment);
        textarea.focus();
        return;
      }

      var commentPassword = requestPasswordIfRequired(saveTrigger);
      if (commentPassword === null) {
        return;
      }

      saveTrigger.disabled = true;
      postAction("heblogs_update_comment", {
        comment_id: commentId,
        content: content,
        comment_password: commentPassword,
      })
        .then(function (result) {
          if (!result || !result.success) {
            var failMessage =
              result && result.data && result.data.message
                ? result.data.message
                : heblogsCommentActions.saveFailed;
            throw new Error(failMessage);
          }

          contentEl.innerHTML = result.data.content;
          toggleEditor(saveCommentItem, false);
        })
        .catch(function (error) {
          window.alert(error.message || heblogsCommentActions.networkError);
        })
        .finally(function () {
          saveTrigger.disabled = false;
        });

      return;
    }

    var deleteTrigger = event.target.closest(".js-comment-delete-trigger");
    if (deleteTrigger) {
      event.preventDefault();

      var deleteCommentItem = closestCommentItem(deleteTrigger);
      if (!deleteCommentItem) {
        return;
      }

      var deleteCommentId = deleteTrigger.getAttribute("data-comment-id");
      if (!deleteCommentId) {
        return;
      }

      if (!window.confirm(heblogsCommentActions.confirmDelete)) {
        return;
      }

      var deletePassword = requestPasswordIfRequired(deleteTrigger);
      if (deletePassword === null) {
        return;
      }

      deleteTrigger.disabled = true;
      postAction("heblogs_delete_comment", {
        comment_id: deleteCommentId,
        comment_password: deletePassword,
      })
        .then(function (result) {
          if (!result || !result.success) {
            var failMessage =
              result && result.data && result.data.message
                ? result.data.message
                : heblogsCommentActions.deleteFailed;
            throw new Error(failMessage);
          }

          deleteCommentItem.remove();
          if (activeInlineReplyEl && deleteCommentItem.contains(activeInlineReplyEl)) {
            activeInlineReplyEl = null;
            activeInlineReplyCommentId = "";
          }
        })
        .catch(function (error) {
          window.alert(error.message || heblogsCommentActions.networkError);
          deleteTrigger.disabled = false;
        });
    }
  });

  document.addEventListener("submit", function (event) {
    var replyForm = event.target.closest(".comment-inline-reply-form");
    if (!replyForm) {
      return;
    }

    event.preventDefault();

    var textarea = replyForm.querySelector("textarea[name='comment']");
    var postIdInput = replyForm.querySelector("input[name='comment_post_ID']");
    var parentIdInput = replyForm.querySelector("input[name='comment_parent']");
    var authorInput = replyForm.querySelector("input[name='author']");
    var passwordInput = replyForm.querySelector("input[name='comment_password']");
    var submitBtn = replyForm.querySelector("input[type='submit'],button[type='submit']");

    if (!textarea || !postIdInput || !parentIdInput) {
      return;
    }

    var payload = {
      comment_post_ID: postIdInput.value,
      comment_parent: parentIdInput.value,
      comment: textarea.value.trim(),
    };

    if (!payload.comment) {
      window.alert(heblogsCommentActions.emptyComment);
      textarea.focus();
      return;
    }

    if (authorInput) {
      payload.author = authorInput.value.trim();
      if (!payload.author) {
        window.alert(heblogsCommentActions.nameNeeded);
        authorInput.focus();
        return;
      }
    }

    if (passwordInput) {
      payload.comment_password = passwordInput.value.trim();
      if (!payload.comment_password) {
        window.alert(heblogsCommentActions.passwordNeeded);
        passwordInput.focus();
        return;
      }
    }

    if (submitBtn) {
      submitBtn.disabled = true;
    }

    postAction("heblogs_add_reply", payload)
      .then(function (result) {
        if (!result || !result.success || !result.data || !result.data.html) {
          var failMessage =
            result && result.data && result.data.message
              ? result.data.message
              : heblogsCommentActions.replyFailed;
          throw new Error(failMessage);
        }

        var parentComment = document.getElementById(
          "comment-" + String(result.data.parentId || payload.comment_parent)
        );
        if (!parentComment) {
          throw new Error(heblogsCommentActions.replyFailed);
        }

        var childrenList = getDirectChildrenList(parentComment);
        if (!childrenList) {
          childrenList = document.createElement("ol");
          childrenList.className = "children";
          parentComment.appendChild(childrenList);
        }

        var temp = document.createElement("div");
        temp.innerHTML = result.data.html;
        var newComment = temp.firstElementChild;
        if (!newComment) {
          throw new Error(heblogsCommentActions.replyFailed);
        }

        if (childrenList.firstElementChild) {
          childrenList.insertBefore(newComment, childrenList.firstElementChild);
        } else {
          childrenList.appendChild(newComment);
        }
        removeInlineReply();
      })
      .catch(function (error) {
        window.alert(error.message || heblogsCommentActions.networkError);
      })
      .finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
        }
      });
  });
})();
