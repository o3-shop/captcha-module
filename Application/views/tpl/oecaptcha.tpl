[{assign var="oCaptcha" value=$oView->getCaptcha()}]
<input type="hidden" name="c_mach" value="[{$oCaptcha->getHash()}]"/>

<div class="form-group verify">
    <label class="req control-label [{$labelCssClass}]" for="c_mac">[{oxmultilang ident="VERIFICATION_CODE"}]</label>

    <div class="[{$inputCssClass}] controls">
        <div class="input-group">
            <span class="input-group-addon">
                <img src="[{$oCaptcha->getImageUrl()}]" alt="">
            </span>
            <input type="text" data-fieldsize="verify" name="c_mac" id="c_mac" value="" class="form-control js-oxValidate js-oxValidate_notEmpty" required>
        </div>
    </div>
</div>