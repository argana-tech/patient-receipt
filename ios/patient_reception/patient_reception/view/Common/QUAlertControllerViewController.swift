//
//  QUAlertControllerViewController.swift
//  patient_reception
//
//  Created by nishimura on 2018/10/19.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit

//QUAlertController.swift
/// 多重呼び出しに対応したUIAlertController. 登録した順に表示します
open class QUAlertController: UIAlertController {
    
    fileprivate static var queue = [QUAlertController]()
    
    /// キューにAlertControllerを追加
    ///
    /// - Parameter alert: QUAlertController
    fileprivate class func addQueue(_ alert: QUAlertController) {
        QUAlertController.queue.insert(alert, at: 0)
    }
    
    /// キューからAlertControllerを削除
    fileprivate class func removeQueue() {
        QUAlertController.queue.removeLast()
    }
    
    /// キューから全て削除
    class func removeAllQueue() {
        QUAlertController.queue.removeAll()
    }
    
    /// 次に表示すべきQUAlertController
    fileprivate class var nextAlertController: QUAlertController? {
        // 末尾を返す
        return queue.last
    }
    
    /// QUAlertControllerを表示します。既に表示中の場合は表示しません。
    fileprivate class func showAlert() {
        DispatchQueue.main.async {
            guard let vc = UIApplication.shared.keyWindow?.rootViewController else {
                return
            }
            let parent = UIViewController.forefrontViewController(vc)
            // 既にAlertControllerが表示中の場合は終了
            if parent is QUAlertController {
                return
            }
            if let alert = nextAlertController {
                parent.present(alert, animated: true) {
                    QUAlertController.removeQueue()
                }
            }
        }
    }
    
    /// QUAlertControllerの表示を実行、または表示登録します。
    open func show() {
        QUAlertController.addQueue(self)
        QUAlertController.showAlert()
    }
    
    /// UIAlertActionを追加
    ///
    /// - Parameters:
    ///   - title: ボタンタイトル
    ///   - style: ボタンスタイル
    ///   - handler: ボタンが選択された時のコールバック
    open func addAction(_ title: String, style: UIAlertActionStyle, handler: ((UIAlertAction) -> Void)? = nil) {
        let action = UIAlertAction(title: title, style: style) { (sender) -> Void in
            if let aHandler = handler {
                aHandler(sender)
            }
            QUAlertController.showAlert()
        }
        super.addAction(action)
    }
    
    /// UIAlertController.addAction(action: UIAlertAction)による追加を防止。使用しないでください。
    ///
    /// - Parameter action: UIAlertAction
    override open func addAction(_ action: UIAlertAction) {
        assert(false, "Deprecated. use -addAction:title:style:handler:")
        return
    }
}

extension UIViewController {
    
    /// 最前面のViewControllerを取得します。表示中のViewControllerと一致するとは限りません。
    ///
    /// - Parameter viewController: 基準となるViewController
    /// - Returns: UIViewController
    class func forefrontViewController(_ viewController: UIViewController) -> UIViewController {
        if let vc = viewController.presentedViewController {
            return UIViewController.forefrontViewController(vc)
        }
        return viewController
    }
}
